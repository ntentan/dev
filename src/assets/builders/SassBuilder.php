<?php
namespace ntentan\dev\assets\builders;

use ntentan\dev\assets\AssetBuilder;

class SassBuilder extends AssetBuilder
{
    public function build() 
    {
        $inputs = implode(' ', $this->inputs);
        passthru("sass $inputs {$this->getOutputFile()}");
    }
}
