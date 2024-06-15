<?php

namespace ntentan\dev\assets;

/**
 * Compiles public assets.
 */
class AssetPipeline 
{
    private static $outputDirectory;
    private static $pipelineFile;
    private static $forcedRebuild;
    
    public static function setup($options)
    {
        self::$outputDirectory = $options['public-dir'];
        self::$pipelineFile = $options['asset-pipeline'];
        self::$forcedRebuild = $options['force'] ?? false;
    }
    
    public static function getAssetsDirectory()
    {
        return self::$outputDirectory;
    }

    public static function define(AssetBuilder ...$builders) 
    {
        $pipelineLastModified = filemtime(self::$pipelineFile);
        error_log("Looking for modified assets to rebuild ...");
        
        foreach($builders as $builder) {
            $outputFile = $builder->getOutputFile();
            if($outputFile == null) {
                error_log("No output path specified for {$builder->getDescription()}");
                continue;
            }
            
            $lastModified = file_exists($outputFile) ? filemtime($outputFile) : time();
            if($builder->hasChanges() || $lastModified < $pipelineLastModified || self::$forcedRebuild) {
                $builder->build();
                error_log("Building asset [{$builder->getOutputFile()}]");
            }
        }
    }
}
