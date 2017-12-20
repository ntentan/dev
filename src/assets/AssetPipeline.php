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
    
    public static function setup($options)
    {
        self::$container = new Container();
        self::$container->bind("js_builder")->to(builders\JsBuilder::class);
        self::$container->bind("css_builder")->to(builders\CssBuilder::class);
        self::$container->bind("copy_builder")->to(builders\CopyBuilder::class);
        self::$container->bind("sass_builder")->to(builders\SassBuilder::class);
        self::$outputDirectory = $options['public-dir'];
        self::$pipelineFile = $options['asset-pipeline'];
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
        foreach($builders as $builder) {
            $outputFile = $builder->getOutputFile();
            $lastModified = file_exists($outputFile) ? filemtime($outputFile) : time();
            if($builder->hasChanges() || $lastModified < $pipelineLastModified) {
                $builder->build();
                error_log("Building asset [{$builder->getOutputFile()}]");
            }
        }
    }
}
