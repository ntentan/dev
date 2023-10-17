<?php
namespace ntentan\dev\assets\builders;

use ntentan\dev\assets\AssetBuilder;
use ScssPhp\ScssPhp\Compiler;

/**
 * Allows the compilation of Sass to CSS in the asset pipeline.
 * 
 * The
 * builder uses only the first Sass file in the input. Other inputs are only
 * used to check for changes. In this regard, the first input file is expected
 * to import the other sass files internally. If this doesn't fit your workflow,
 * you can force the asset builder to always build the sass files by setting
 * always_build to true. By doing this however, the asset to which the sass file
 * belongs would always be rebuilt when requested during development. All these
 * quirks do not apply when assets are built and bundled for deployment.
 */
class SassBuilder extends AssetBuilder
{
    private $sassCompiler;

    public function __construct(Compiler $compiler)
    {
        $this->sassCompiler = $compiler;
    }

    public function build(): void
    {
        $code = "";
        foreach($this->expandInputs() as $input) {
            $input = addslashes($input);
            $code .= "@import \"$input\";\n";
        }
        file_put_contents($this->getOutputFile(), $this->sassCompiler->compileString($code)->getCss());
    }

    public function getDescription(): string {
        return "SCSS";
    }
}
