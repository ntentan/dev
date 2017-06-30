<?php
namespace ntentan\dev\assets\builders;

use ntentan\dev\assets\AssetBuilder;

class CssBuilder extends AssetBuilder
{
    public function build() {
        $output = "";
        foreach($this->inputs as $input) {
            $output .= "\n". file_get_contents($input);
        }
        file_put_contents($this->getOutputFile(), $output);
    }
}