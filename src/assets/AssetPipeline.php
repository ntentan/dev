<?php

namespace ntentan\dev\assets;

use ntentan\panie\Container;

/**
 * Compiles assets.
 */
class AssetPipeline 
{
    private static $outputDirectory;
    private static $container;
    
    public static function setup($outputDirectory) 
    {
        self::$container = new Container();
        self::$container->bind("js_builder")->to(builders\JsBuilder::class);
        self::$container->bind("css_builder")->to(builders\CssBuilder::class);
        self::$container->bind("copy_builder")->to(builders\CopyBuilder::class);
        self::$outputDirectory = $outputDirectory;
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
        foreach($builders as $builder) {
            if($builder->hasChanges()) {
                $builder->build();
            }
        }
    }
}
