<?php

namespace ntentan\dev\commands;

class Serve implements \clearice\Command
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
                    'help' => 'Server port value (defaults to 8080)'
                ],
                [
                    'short' => 'h', 'long' => 'host', 'has_value' => true, 
                    'help' => 'Host to be bound to (defaults to 127.0.0.1)'
                ]
            ]
        ];
    }
    
    public function run($options)
    {
        $spec = [STDOUT, STDIN, STDERR];
        $pipes = [];
        $process = proc_open(
            PHP_BINARY . " -S 127.0.0.1:8080 " . __DIR__ . "/../../utils/router.php", 
            $spec, $pipes
        );
        while(proc_get_status($process)['running']) {
            usleep(500);
        }
    }
}