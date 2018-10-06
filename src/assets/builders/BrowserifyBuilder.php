<?php
namespace ntentan\dev\assets\builders;

use ntentan\dev\assets\AssetBuilder;


class BrowserifyBuilder extends AssetBuilder
{
    private $entry;
    private $inputs = [];

    public function setInputs($inputs)
    {
        $this->entry = $inputs[0];
        exec("browserify {$this->entry} --list", $this->inputs);
    }

    public function getInputs()
    {
        return $this->inputs;
    }

    public function build()
    {
        passthru("browserify {$this->entry} -d > {$this->getOutputFile()}");
    }
}
