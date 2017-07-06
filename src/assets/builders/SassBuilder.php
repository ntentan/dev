<?php
namespace ntentan\dev\assets\builders;

use ntentan\dev\assets\AssetBuilder;

/**
 * Allows the compilation of Sass to CSS in the asset pipeline.
 * 
 * This builder uses a call to a sass compiler installed on the system. The
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
    public function build() 
    {
        passthru("sassc {$this->inputs[0]} {$this->getOutputFile()}");
    }
}
