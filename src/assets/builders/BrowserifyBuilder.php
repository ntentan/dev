<?php
namespace ntentan\dev\assets\builders;

use ntentan\dev\assets\AssetBuilder;


class BrowserifyBuilder extends AssetBuilder
{
    private $entry;
    private $inputs = [];

    public function setInputs(array $inputs)
    {
        $this->entry = $inputs[0];
        exec("browserify {$this->entry} --list", $this->inputs);
    }

    public function getInputs(): array
    {
        return $this->inputs;
    }

    public function build(): void
    {
        passthru("browserify {$this->entry} -d > {$this->getOutputFile()}");
    }
}
