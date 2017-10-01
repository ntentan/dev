<?php

namespace ntentan\dev\commands;

use ntentan\dev\assets\AssetPipeline;

class Build
{
    private $assetPipeline;

    public function __construct(AssetPipeline $assetPipeline)
    {
        $this->assetPipeline = $assetPipeline;
    }

    public function run($options)
    {
        if (file_exists('asset_pipeline.php')) {
            $this->assetPipeline->setup($options['public-dir']);
            require $options['asset-pipeline'];
        }
    }

}
