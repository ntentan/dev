<?php

namespace ntentan\dev\commands;

class Serve implements \clearice\CommandInterface
{
    public static function getCommandOptions()
    {
        return [
            'command' => 'serve', 
            'help' => 'Start the internal php server', 
            'class' => '\ntentan\dev\commands\Serve',
            'options' =>[
                [
                    'short' => 'p', 'long' => 'port', 'has_value' => true, 
                    'help' => 'Server port value (defaults to 8080)',
                    'default' => '8080'
                ],
                [
                    'short' => 'h', 'long' => 'host', 'has_value' => true, 
                    'help' => 'Host to be bound to (defaults to 127.0.0.1)',
                    'default' => '127.0.0.1'
                ],
                [
                    'short' => 'b', 'long' => 'insert-breakpoints',
                    'help' => 'inserts an exception handler which callsed xdebug_break()'
                ]
            ]
        ];
    }
    
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
            PHP_BINARY . " -S {$options['host']}:{$options['port']} " . __DIR__ . "/../../src/Router.php", 
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
