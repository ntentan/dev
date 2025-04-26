<?php

namespace ntentan\dev\commands;

class Debug extends Serve
{
    protected string $phpBinaryArgs = '-dxdebug.mode=debug -dxdebug.client_port=9003 -dxdebug.client_host=127.0.0.1';
    
    #[\Override]
    public function run($options): int {
        if (isset($options['dump-router']) && $options['dump-router']) {
            echo "{$this->buildCommand($options)}\n";
            return 0;
        } else {
            return parent::run($options);
        }
    }
}