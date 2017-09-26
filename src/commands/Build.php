<?php

namespace ntentan\dev\commands;

use ntentan\dev\assets\AssetPipeline;

class Build
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
