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
        if (file_exists($options['asset-pipeline'])) {
            $this->assetPipeline->setup($options);
            require $options['asset-pipeline'];
        }
    }
}
