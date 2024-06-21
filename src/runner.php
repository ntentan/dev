<?php
use ntentan\dev\assets\AssetBuilder;
use ntentan\dev\assets\AssetPipeline;
use ntentan\dev\assets\builders\CopyBuilder;
use ntentan\dev\assets\builders\SassBuilder;
use ntentan\utils\Filesystem;
use ScssPhp\ScssPhp\Compiler;


function runAssetBuilder(
        string $pipeline = __DIR__ . '/../../../../bootstrap/assets.php', 
        bool $forceRebuild = false,
        string $cachePath = __DIR__ . "/../../../../.ntentan-build"
    ) {
    
    // Build assets from the project home directory
    AssetPipeline::setup([
        'public-dir' => 'public', 
        'asset-pipeline' => $pipeline,
        'force' => $forceRebuild
    ]);

    AssetBuilder::register("sass", function() use ($cachePath) {
        $builder = new SassBuilder(new Compiler());
        Filesystem::directory($cachePath)->createIfNotExists();
        $builder->setCachePath($cachePath); 
        return $builder;
    });

    AssetBuilder::register("copy", fn() => new CopyBuilder());
    
    require $pipeline;
}