<?php
namespace ntentan\dev\assets\builders;

use ntentan\dev\assets\AssetBuilder;
use ScssPhp\ScssPhp\Compiler;
use ntentan\kaikai\Cache;
use ntentan\utils\FileSystem;

/**
 * Processes SCSS files into CSS to be bundled with other stylesheets and served.
 */
class SassBuilder extends AssetBuilder
{
    private Compiler $sassCompiler;
    private array $filesTouched;
    private string $cachePath;

    public function __construct(Compiler $compiler)
    {
        $this->sassCompiler = $compiler;
        $this->filesTouched = [];
        $options = $this->getOptions();
        $this->sassCompiler->setImportPaths(array_merge([
            function ($script) {
                if (Compiler::isCssImport($script)) {
                    return null;
                }
                $importPath = realpath(dirname($this->getInputs()[0]));
                return $this->findPaths($importPath, $script);
            }
        ], [$options['include_paths'] ?? []]));
    }

    public function setCachePath(string $cachePath) {
        $this->cachePath = $cachePath;
    }

    private function findPaths(string $importPath, string $script) {
        $hasExtension = strlen($importPath) > 5 && substr($importPath, -5) == ".scss";

        // Add an an extension and return path
        if (!$hasExtension) {
            $script .= ".scss";
        }
        
        $targetFiles = ["$importPath/$script", "$importPath/_$script"];
        foreach($targetFiles as $targetFile) {
            if(file_exists($targetFile)) {
                $this->filesTouched[]= $targetFile;
                return $targetFile;
            }
        }

        return null;
    }

    public function hasChanges () : bool
    {
        $outputFile = $this->getOutputFile();
        $outputFileCache = sprintf("%s/%s.scssbuild", $this->cachePath, md5($outputFile));
        $outputModificationTime = filemtime($outputFile);

        if (!file_exists($outputFileCache)) {
            return true;
        } 

        $touchedFile = json_decode(file_get_contents($outputFileCache));

        foreach($touchedFile as $file) {
            if ($outputModificationTime < filemtime($file)) {
                error_log("$file modified ...");
                return true;
            }
        }
        
        return parent::hasChanges($outputFile);
    }

    public function build(): void
    {
        $code = "";
        foreach($this->expandInputs() as $input) {
            $code .= $this->sassCompiler->compileString(file_get_contents($input))->getCss();
        }

        $outputFile = $this->getOutputFile();
        file_put_contents(sprintf("%s/%s.scssbuild", $this->cachePath, md5($outputFile)), json_encode($this->filesTouched));
        file_put_contents($outputFile, $code);
    }

    public function getDescription(): string {
        return "scss";
    }
}
