<?php

namespace ntentan\dev\assets;

/**
 * Helps in defining the asset pipeline.
 */
class AssetPipeline 
{
    
    private static $outputDirectory;

    public static function define(AssetBuilder ...$builders) 
    {
        foreach($builders as $builder) {
            $builder->setOutputDirectory(self::$outputDirectory);
            if($builder->hasChanges()) {
                $builder->build();
            }
        }
    }

    public static function setOutputDirectory($outputDirectory) 
    {
        self::$outputDirectory = $outputDirectory;
    }
}