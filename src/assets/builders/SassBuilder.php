<?php
namespace ntentan\dev\assets\builders;

use ntentan\dev\assets\AssetBuilder;
use ScssPhp\ScssPhp\Compiler;

/**
 * Builds SCSS files into CSS.
 */
class SassBuilder extends AssetBuilder
{
    private $sassCompiler;

    public function __construct(Compiler $compiler)
    {
        $this->sassCompiler = $compiler;
        $this->sassCompiler->setImportPaths([
            function ($path) {
                if (Compiler::isCssImport($path)) {
                    return null;
                }

                $inputPath = realpath(dirname($this->getInputs()));

                if (file_exists($path)) {
                    return $this->getInputs()
                }
            }
        ]);
    }

    public function hasChanges()
    {
        for () {

        }
        return super::hasChanges();
    }

    public function build(): void
    {
        $code = "";
        foreach($this->expandInputs() as $input) {
            $code .= $this->sassCompiler->compile($input)->getCss();
        }
        file_put_contents($this->getOutputFile(), $code);
    }

    public function getDescription(): string {
        return "scss";
    }
}
