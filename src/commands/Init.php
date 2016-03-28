<?php

namespace ntentan\dev\commands;
use clearice\ClearIce;
use clearice\Command;
use ntentan\dev\TaskRunner;
use anyen\Runner;
use ntentan\utils\Filesystem;

class Init extends TaskRunner implements Command
{
    public static function getCommandOptions()
    {
        return [
            'command' => 'init',
            'help' => 'Initialize this directory with the ntentan framework',
            'options' => [
                [
                    'short' => 'n',
                    'long' => 'namespace',
                    'help' => 'Specify the namespace of the app\'s classes'
                ]
            ]
        ];
    }
    
    public function run($options)
    {
        if(isset($options['namespace'])) {
            $this->runTask($options);
        } else {
            Runner::run(
                __DIR__ . '/../../wizards/init.wizard.php',
                ['callback' => $this]
            );
        }
        
    }

    public function runTask($options)
    {
        ClearIce::output("Setting up app namespace {$options['namespace']} ... ");
        Filesystem::createDirectoryStructure(
            [
                'src' => 
                    [
                        'modules' => ['home'], 
                        'lib'
                    ],
                'config',
                'logs',
                'views' => ['default', 'home']
            ],
            './'
        );
        $data = ['namespace' => $options['namespace'], 'date' => date("Y-m-d H:i:s") ];
        $this->writeFile(
            __DIR__ . "/../../code_templates/misc/htaccess.template", 
            $data, '.htaccess'
        );
        $this->writeFile(
            __DIR__ . "/../../code_templates/php/index.php.template", 
            $data, 'index.php'
        );
        $this->writeFile(
            __DIR__ . "/../../code_templates/php/HomeController.php.template", 
            $data, 'src/modules/home/HomeController.php'
        );       
        $this->writeFile(
            __DIR__ . "/../../code_templates/php/home_index.tpl.php.template", 
            $data, 'views/default/home_index.tpl.php'
        );  
        $this->writeFile(
            __DIR__ . "/../../code_templates/php/main.tpl.php.template", 
            $data, 'views/default/main.tpl.php'
        );          
        
        ClearIce::output("OK\n");
    }
}

