<?php

namespace ntentan\dev\commands;

class Test implements \clearice\Command
{
    public function run($options)
    {
        $phpunit = new \PHPUnit_TextUI_Command();
        $phpunit->run(['', 'tests/cases']);
    }

}

