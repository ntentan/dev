<?php

namespace ntentan\dev\assets\builders;

use ntentan\dev\assets\AssetBuilder;

/**
 * Copies asset files from the input to the output directory.
 * 
 */
class CopyBuilder extends AssetBuilder
{
    
    public function build() 
    {
        $outputDirectory = $this->getOutputFile();
        
        if(!file_exists($outputDirectory)){
            mkdir($outputDirectory);
        } else {
            touch($outputDirectory);
        }
        
        $files = $this->expandInputs();
        
        foreach($files as $file) {
            copy($file, $outputDirectory . '/' . basename($file));
        }
    }

}
