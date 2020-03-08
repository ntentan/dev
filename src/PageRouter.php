<?php

// Must always run in the vendor directory
require __DIR__ . '/../../../autoload.php';

use ntentan\honam\TemplateEngine;
use ntentan\dev\assets\AssetPipeline;

new class {
    
    private $config;
    
    public function __construct() 
    {
        $this->config = json_decode(file_get_contents('../.ntentan-dev.json'), true);
        $requestUri = filter_input(INPUT_SERVER, 'REQUEST_URI');
        $requestFile = explode('?', $requestUri)[0];

        if($requestUri == '/' && !file_exists('index.php')) {
            require __DIR__ . "/../installer/setup.php";
            die();
        }


        if(!is_file($this->config['docroot'] . '/' . $requestFile)) {
            //set_exception_handler([$this, 'exceptionHandler']);
            error_log("Serving: $requestUri");
            if($this->rebuildAssets()){
                AssetPipeline::setup(['public-dir' => 'public', 'asset-pipeline' => __DIR__ . '/../../../../bootstrap/assets.php']);
                require __DIR__ . '/../../../../bootstrap/assets.php';
            }
            require __DIR__ . '/../../../../public/index.php';
            die();
        }
    }

    private function rebuildAssets() {
        $client = strtolower(filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH'));
        return file_exists(__DIR__ . '/../../../../bootstrap/assets.php')
            && !isset($this->config['disable-asset-builder'])
            && $client != 'xmlhttprequest';
    }
};

// If we made it this far then yield so php handles the rest
return false;
