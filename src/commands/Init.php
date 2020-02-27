<?php


namespace ntentan\dev\commands;
use clearice\io\Io;
use ntentan\dev\tasks\Initialize;


class Init
{
    private $io;
    private $initializeTask;

    public function __construct(Io $io, Initialize $initializeTask)
    {
        $this->io = $io;
        $this->initializeTask = $initializeTask;
    }

    public function run($options)
    {

    }
}