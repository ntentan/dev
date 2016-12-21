<?php

namespace ntentan\dev\commands;

class Test implements \clearice\CommandInterface
{
    public static function getCommandOptions()
    {
        return [
            'command' => 'test',
            'help' => 'Run tests on your application',
            'options' => []
        ];
    }
    
    public function run($options)
    {
        $phpunit = new \PHPUnit_TextUI_Command();
        $phpunit->run([
            '', 'tests/cases', 
            '--bootstrap=tests/bootstrap.php'
        ]);
    }

}

