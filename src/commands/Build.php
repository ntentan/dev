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
        if (!file_exists($options['pipeline-path'])) {
            $this->io->error( "Asset pipeline file not found: {$options['pipeline-path']}\n");
            exit(1);
        }
        runAssetBuilder($options);
    }
}
