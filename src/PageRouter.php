<?php

// Must always run in the vendor directory
require __DIR__ . '/../../../autoload.php';

use ntentan\dev\assets\AssetPipeline;

/**
 * An anonymous class that implements the page routing logic.
 */
new class {
    
    private $config;
    
    public function __construct() 
    {
        if(file_exists('../.ntentan-dev.json')) {
            $this->config = json_decode(file_get_contents('../.ntentan-dev.json'), true);
        } else if (file_exists('.ntentan-dev.json')) {
            $this->config = json_decode(file_get_contents('.ntentan-dev.json'), true);
        }

        $requestUri = filter_input(INPUT_SERVER, 'REQUEST_URI');
        $requestFile = explode('?', $requestUri)[0];

        if($requestUri == '/' && !file_exists('index.php')) {
            require __DIR__ . "/../installer/setup.php";
            die();
        }


        if(!is_file(($_SERVER["DOCUMENT_ROOT"] ?? ".") . '/' . $requestFile)) {
            error_log("Serving: $requestUri");
            if($this->rebuildAssets()){
                // Build assets from the project home directory
                AssetPipeline::setup(['public-dir' => 'public', 'asset-pipeline' => __DIR__ . '/../../../../bootstrap/assets.php']);
                require __DIR__ . '/../../../../bootstrap/assets.php';

                // Return to the public directory
                chdir("public");
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
