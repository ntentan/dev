<?php

namespace ntentan\dev\commands;

use clearice\io\Io;
use ntentan\dev\assets\AssetPipeline;

/**
 * Build
 */
class Build
{
    private $assetPipeline;
    private $io;

    public function __construct(Io $io, AssetPipeline $assetPipeline)
    {
        $this->assetPipeline = $assetPipeline;
        $this->io = $io;
    }

    public function run($options)
    {
        if (file_exists($options['asset-pipeline'])) {
            AssetPipeline::setup($options);
            require $options['asset-pipeline'];
            return 0;
        } else {
            $this->io->error("Cannot find asset pipeline {$options['asset-pipeline']}\n");
            return 10;
        }
    }
}
