<?php

namespace ntentan\dev\assets\builders;

use ntentan\dev\assets\AssetBuilder;
use ntentan\utils\Filesystem;

/**
 * Copies asset files from the input to the output directory.
 * 
 */
class CopyBuilder extends AssetBuilder
{
    public function build(): void
    {
        $outputPath = $this->getOutputFile();

        if ((str_ends_with($outputPath, "\\") || str_ends_with($outputPath, "/")) && !file_exists($outputPath)) {
            Filesystem::directory($outputPath)->createIfNotExists();
            $destination = $outputPath;
        } else {
            $destination = dirname($outputPath);
        }
        
        $files = $this->expandInputs();

        foreach ($files as $file) {
            copy($file, $destination . DIRECTORY_SEPARATOR . basename($file));
        }
    }

    public function getDescription(): string
    {
        return "copier";
    }
}
