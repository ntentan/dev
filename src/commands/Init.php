<?php

namespace ntentan\dev\commands;

use clearice\ConsoleIO;
use ntentan\dev\TaskRunner;
use anyen\Runner;
use ntentan\utils\Filesystem;

class Init extends TaskRunner
{
    private $io;

    public function __construct(ConsoleIO $io)
    {
        $this->io = $io;
    }

    public function run($options)
    {
        if (isset($options['namespace'])) {
            $this->runTask($options);
        } else {
            Runner::run(
                    __DIR__ . '/../../wizards/init.wizard.php', ['callback' => $this]
            );
        }
    }

    public function runTask($options)
    {
        $this->io->output("Setting up app namespace {$options['namespace']} ... ");
        Filesystem::createDirectoryStructure(
                [
            'src' =>
            [
                'controllers',
                'lib'
            ],
            'config',
            'logs',
            'views' => ['layouts', 'home', 'shared'],
            'temp'
                ], './'
        );
        $data = ['namespace' => $options['namespace'], 'date' => date("Y-m-d H:i:s")];
        $this->writeFile(__DIR__ . "/../../templates/code/misc/htaccess.template", $data, '.htaccess');
        $this->writeFile(__DIR__ . "/../../templates/code/php/index.php.template", $data, 'index.php');
        $this->writeFile(__DIR__ . "/../../templates/code/php/HomeController.php.template", $data, 'src/controllers/HomeController.php');
        $this->writeFile(__DIR__ . "/../../templates/code/php/home_index.tpl.php.template", $data, 'views/home/index.tpl.php');
        $this->writeFile(__DIR__ . "/../../templates/code/php/main.tpl.php.template", $data, 'views/layouts/main.tpl.php');

        $this->io->output("OK\n");
    }

}
