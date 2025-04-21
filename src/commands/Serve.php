<?php

namespace ntentan\dev\commands;

/**
 * A command that runs PHP's internal web server around ntentan.
 */
class Serve
{
    protected string $phpBinaryArgs = '';

    public function run($options)
    {
        declare(ticks = 1)

        // This just prevents a clean shutdown on systems that have no PCNTL support
        if(function_exists('pcntl_signal')) { 
            pcntl_signal(SIGINT, [$this, 'shutdown']);
        }

        // Resolve the document root
        $docroot = realpath(__DIR__ . "/../../../../../public");
        $docroot = $docroot === false ? '.' : $docroot;
        $options['docroot'] = $docroot;
        file_put_contents('.ntentan-dev.json', json_encode($options));

        // Setup and start the server process
        $spec = [STDOUT, STDIN, STDERR];
        $pipes = [];
        $process = proc_open(
            PHP_BINARY . " {$this->phpBinaryArgs} -d cli_server.color=1 -t {$docroot} -S {$options['host']}:{$options['port']} " . __DIR__ . "/../../src/router.php",
            $spec, $pipes
        );
        while(proc_get_status($process)['running']) {
            usleep(500);
        }
        $this->shutdown();
        return 0;
    }    
    
    private function shutdown()
    {
        print "\nShutting down ... ";
        if(file_exists('.ntentan-dev.json')) {
            unlink('.ntentan-dev.json');
        }
        print "OK\n";
    }
}
