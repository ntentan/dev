<?php
// Must always run in the vendor directory

require __DIR__ . '/../../../autoload.php';
require_once __DIR__ . '/runner.php';


function rebuildAssets() {
    $client = strtolower(filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH') ?? "");
    return file_exists(__DIR__ . '/../../../../bootstrap/assets.php')
        && !isset($this->config['disable-asset-builder'])
        && $client != 'xmlhttprequest';
}

  
function run() 
{
    $config = [];

    if(file_exists('../.ntentan-dev.json')) {
        $config = json_decode(file_get_contents('../.ntentan-dev.json'), true);
    } else if (file_exists('.ntentan-dev.json')) {
        $config = json_decode(file_get_contents('.ntentan-dev.json'), true);
    }

    set_exception_handler(function (Throwable $exception) {
        require "exception.php";
        die();
    });

    $requestUri = filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL) ?? "";
    $requestFile = explode('?', $requestUri)[0];

    if($requestUri == '/' && !file_exists('src/main.php')) {
        require __DIR__ . "/../installer/setup.php";
        die();
    }

    if(!is_file(($_SERVER["DOCUMENT_ROOT"] ?? ".") . '/' . urldecode($requestFile))) {
        error_log("Serving: $requestUri");
        if(rebuildAssets()) {
            runAssetBuilder();
        }
        $indexFile = __DIR__ . '/../../../../src/main.php';
        if (file_exists($indexFile)) {
            require $indexFile;
        }
        die();
    }
    
    return false;
}

//};

// If we made it this far then yield so php handles the rest
return run();
