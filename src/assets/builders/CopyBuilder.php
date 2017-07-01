<?php

namespace ntentan\dev\assets\builders;

use ntentan\dev\assets\AssetBuilder;

class CopyBuilder extends AssetBuilder
{
    
    public function build() 
    {
        $outputDirectory = $this->getOutputFile();
        mkdir($outputDirectory);
        $files = $this->expandInputs();
        foreach($files as $file) {
            copy($file, $outputDirectory . '/' . basename($file));
        }
    }

}
