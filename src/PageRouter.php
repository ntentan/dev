<?php

require 'vendor/autoload.php';

use ntentan\honam\TemplateEngine;
use ntentan\dev\assets\AssetPipeline;

new class {
    
    private $config;
    
    public function __construct() {
        $this->config = json_decode(file_get_contents('~ntentan.dev.config.json'), true);
        $requestUri = filter_input(INPUT_SERVER, 'REQUEST_URI');
        $requestFile = explode('?', $requestUri)[0];
        if(!(is_file(getcwd() . $requestFile) || $requestFile == '/favicon.ico' )) {
            //set_exception_handler([$this, 'exceptionHandler']);
            error_log("Serving: $requestUri");
            if($this->rebuildAssets()){
                AssetPipeline::setup(['public-dir' => 'public', 'asset-pipeline' => 'asset_pipeline.php']);
                require 'asset_pipeline.php';
            }
            require 'index.php';
            die();
        }
    }

    private function rebuildAssets() {
        $client = strtolower(filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH'));
        return file_exists('asset_pipeline.php')
            && !isset($this->config['disable-asset-builder'])
            && $client != 'xmlhttprequest';
    }
};

// If we made it this far then yield so php handles the rest
return false;