<?php

namespace ntentan\dev\commands;

class Serve implements \clearice\Command
{
    public function run($options)
    {
        $spec = [STDOUT, STDIN, STDERR];
        $pipes = [];
        $process = proc_open(
            PHP_BINARY . " -S 0.0.0.0:8080 " . __DIR__ . "/../utils/router.php", 
            $spec, $pipes
        );
        while(proc_get_status($process)['running']) {
            usleep(500);
        }
    }
}