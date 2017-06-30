<?php

namespace ntentan\dev\assets\builders;

use ntentan\dev\assets\AssetBuilder;

class CopyBuilder extends AssetBuilder
{
    
    public function build() 
    {
        $outputDirectory = $this->getOutputFile();
        mkdir($outputDirectory);
        foreach($this->inputs as $input) {
            $files = glob($input);
            foreach($files as $file) {
                copy($file, $outputDirectory . '/' . basename($file));
            }
        }
    }

}
