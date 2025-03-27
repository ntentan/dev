<?php
// Must always run in the vendor directory

require __DIR__ . '/../../../autoload.php';
require_once __DIR__ . '/builder.php';


function run(): bool
{
    $homeDirectory = __DIR__ . "/../../../..";
    $runtimeConfig = "$homeDirectory/.ntentan-dev.json";
    chdir("$homeDirectory/public/");

    $config = [];
    if(file_exists($runtimeConfig)) {
        $config = json_decode(file_get_contents($runtimeConfig), true);
    }

    set_exception_handler(function (Throwable $exception) {
        http_response_code(500);
        require "exception.php";
        die();
    });

    $requestUri = filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL) ?? "";
    $requestFile = "$homeDirectory/public/" . explode("?", $requestUri)[0];

    // Force the ntentan installer if the application has not been setup
    if($requestUri == '/' && !file_exists("$homeDirectory/src/php/main.php")) {
        require __DIR__ . "/../installer/setup.php";
        die();
    }

    // Skip existing files so they could be served up later.
    if(!is_file($requestFile)) {
        error_log("Serving: {$_SERVER['REQUEST_URI']}");
        $assetPipeline = "$homeDirectory/src/php/assets.php";

        if (file_exists($assetPipeline)
                && !isset($config['disable-asset-builder'])
                && strtolower(filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH') ?? "") != 'xmlhttprequest'
            ) {
            runAssetBuilder([
                'pipeline-path' => $assetPipeline,
                'public-path' => "$homeDirectory/public",
                'cache-path' => "$homeDirectory/.ntentan-build",
                'assets-path' => $homeDirectory
            ]);
        }

        $indexFile = "$homeDirectory/src/php/main.php";
        if (file_exists($indexFile)) {
            require $indexFile;
        }
        die();
    }
    
    // Check and send the appropriate headers for gzip compressed javascript and css files
    $fileParts = explode(".", strtolower($requestFile));
    $n = count($fileParts);
    if (($fileParts[$n - 1] == 'gz' || $fileParts[$n - 1] == 'br') && in_array($fileParts[$n - 2], ['css', 'js', 'wasm'])) {
        header("Content-Encoding: " . 
            match($fileParts[$n - 1]) {
                'gz' => 'gzip',
                'br' => 'br'
            });
        header("Content-type: " . 
            match($fileParts[$n - 2]) {
                'css' => 'text/css',
                'js'=> 'application/js',
                'wasm' => 'application/wasm'
            });
        echo file_get_contents($requestFile);
        die();
    }   
    
    return false;
}

// If we made it this far then yield so php handles the rest
return run();

