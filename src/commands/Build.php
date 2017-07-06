<?php

namespace ntentan\dev\commands;

use clearice\CommandInterface;
use ntentan\dev\assets\AssetPipeline;

class Build implements CommandInterface
{
    
    public static function getCommandOptions()
    {
        return [
            'command' => 'build', 
            'help' => 'Build the assets required for the application', 
            'class' => '\ntentan\dev\commands\Build'
        ];
    }
    
    public function run($options)
    {
        if(file_exists('asset_pipeline.php')){
            AssetPipeline::setup('public');
            require 'asset_pipeline.php';
        }        
    }

}
