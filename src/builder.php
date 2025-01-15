<?php
use ntentan\dev\assets\AssetBuilder;
use ntentan\dev\assets\AssetPipeline;
use ntentan\dev\assets\builders\CopyBuilder;
use ntentan\dev\assets\builders\SassBuilder;
use ntentan\utils\Filesystem;
use ScssPhp\ScssPhp\Compiler;
use ntentan\dev\assets\builders\JsBuilder;


function runAssetBuilder(array $config): void
{
    $pipeline = $config['pipeline-path'] ?? "src/php/assets.php";
    $cachePath = $config["cache-path"] ?? ".ntentan-build";
    
    error_log("Looking for assets to rebuild.");
    
    AssetPipeline::setup($config);
    AssetBuilder::register("sass", function() use ($cachePath) {
        $builder = new SassBuilder(new Compiler());
        Filesystem::directory($cachePath)->createIfNotExists();
        $builder->setCachePath($cachePath); 
        return $builder;
    });
    AssetBuilder::register("js", fn() => new JsBuilder());
    AssetBuilder::register("copy", fn() => new CopyBuilder());
    
    require $pipeline;
    
    AssetPipeline::run();
}