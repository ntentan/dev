<?php

namespace ntentan\dev\commands;

class Serve
{    
    public function run($options)
    {
        declare(ticks = 1)
        pcntl_signal(SIGINT, [$this, 'shutdown']);
        $spec = [STDOUT, STDIN, STDERR];
        $pipes = [];
        $config = [
            'break-points' => $options['insert-breakpoints'] ?? false
        ];
        file_put_contents('~ntentan.dev.config.json', json_encode($config));
        $process = proc_open(
            PHP_BINARY . " -d cli_server.color=1 -S {$options['host']}:{$options['port']} " . __DIR__ . "/../../src/PageRouter.php", 
            $spec, $pipes
        );
        while(proc_get_status($process)['running']) {
            usleep(500);
        }
        $this->shutdown();
    }    
    
    private function shutdown()
    {
        print "\nShutting down ... ";
        unlink('~ntentan.dev.config.json');
        print "OK\n";
    }
}
