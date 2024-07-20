<?php

namespace ntentan\dev\commands;

require_once __DIR__ . '/../builder.php';

use clearice\io\Io;
use ntentan\dev\assets\AssetPipeline;


class Build
{
    private $io;

    public function __construct(Io $io)
    {
        $this->io = $io;
    }

    public function run($options)
    {
        if (file_exists($options['asset-pipeline'])) {
            runAssetBuilder($options['asset-pipeline'], true);
            return 0;
        } else {
            $this->io->error("Cannot find asset pipeline {$options['asset-pipeline']}\n");
            return 10;
        }
    }
}
