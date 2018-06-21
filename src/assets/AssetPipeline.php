<?php

namespace ntentan\dev\assets;

use ntentan\panie\Container;

/**
 * Compiles assets.
 */
class AssetPipeline 
{
    private static $outputDirectory;
    private static $pipelineFile;
    private static $container;
    private static $forcedRebuild;
    
    public static function setup($options)
    {
        self::$container = new Container();
        self::$container->bind("js_builder")->to(builders\JsBuilder::class);
        self::$container->bind("css_builder")->to(builders\CssBuilder::class);
        self::$container->bind("copy_builder")->to(builders\CopyBuilder::class);
        self::$container->bind("sass_builder")->to(builders\SassBuilder::class);
        self::$container->bind("browserify_builder")->to(builders\BrowserifyBuilder::class);
        self::$outputDirectory = $options['public-dir'];
        self::$pipelineFile = $options['asset-pipeline'];
        self::$forcedRebuild = $options['force'] ?? false;
    }
    
    public static function getAssetsDirectory()
    {
        return self::$outputDirectory;
    }
    
    public static function getContainer() 
    {
        return self::$container;
    }

    public static function define(AssetBuilder ...$builders) 
    {
        $pipelineLastModified = filemtime(self::$pipelineFile);
        error_log("Looking for modified assets to rebuild ...");
        foreach($builders as $builder) {
            $outputFile = $builder->getOutputFile();
            $lastModified = file_exists($outputFile) ? filemtime($outputFile) : time();
            if($builder->hasChanges() || $lastModified < $pipelineLastModified || self::$forcedRebuild) {
                $builder->build();
                error_log("Building asset [{$builder->getOutputFile()}]");
            }
        }
    }
}
