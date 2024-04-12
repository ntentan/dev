<?php
namespace ntentan\dev\assets\builders;

use ntentan\dev\assets\AssetBuilder;
use ScssPhp\ScssPhp\Compiler;
use ntentan\kaikai\Cache;

/**
 * Builds SCSS files into CSS.
 */
class SassBuilder extends AssetBuilder
{
    private Compiler $sassCompiler;
    private array $filesTouched;
    private Cache $cache;

    public function __construct(Compiler $compiler)
    {
        $this->sassCompiler = $compiler;
        $this->filesTouched = [];
        $this->sassCompiler->setImportPaths([
            function ($script) {
                if (Compiler::isCssImport($script)) {
                    return null;
                }
                $importPath = realpath(dirname($this->getInputs()[0]));
                return $this->findPaths($importPath, $script);
            }
        ]);
    }

    public function setCache(Cache $cache) {
        $this->cache = $cache;
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
        $outputModificationTime = filemtime($outputFile);
        if ($outputFile && $this->cache && $this->cache->exists($outputFile)) {
            foreach($this->cache->read($outputFile, fn() => []) as $file) {
                if ($outputModificationTime < filemtime($file)) {
                    return true;
                }
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
        if ($this->cache) {
            $this->cache->write($this->getOutputFile(), $this->filesTouched);
        }
        file_put_contents($this->getOutputFile(), $code);
    }

    public function getDescription(): string {
        return "scss";
    }
}
