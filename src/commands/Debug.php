<?php

namespace ntentan\dev\commands;

class Debug extends Serve
{
    protected string $phpBinaryArgs = '-dxdebug.mode=debug -dxdebug.client_port=9003 -dxdebug.client_host=127.0.0.1';
}