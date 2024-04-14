<?php
// Must always run in the vendor directory

require __DIR__ . '/../../../autoload.php';

use ntentan\dev\assets\AssetBuilder;
use ntentan\dev\assets\AssetPipeline;
use ntentan\dev\assets\builders\CopyBuilder;
use ntentan\dev\assets\builders\SassBuilder;
use ntentan\kaikai\backends\FileCache;
use ntentan\utils\Filesystem;
use ntentan\kaikai\Cache;
use ScssPhp\ScssPhp\Compiler;

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

        set_exception_handler([$this, "exceptionHandler"]);

        $requestUri = filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL) ?? "";
        $requestFile = explode('?', $requestUri)[0];

        if($requestUri == '/' && !file_exists('index.php')) {
            require __DIR__ . "/../installer/setup.php";
            die();
        }

        if(!is_file(($_SERVER["DOCUMENT_ROOT"] ?? ".") . '/' . urldecode($requestFile))) {
            error_log("Serving: $requestUri");
            if($this->rebuildAssets()) {
                // Build assets from the project home directory
                AssetPipeline::setup([
                    'public-dir' => 'public', 
                    'asset-pipeline' => __DIR__ . '/../../../../bootstrap/assets.php'
                ]);
                AssetBuilder::register("sass", function() {
                    $builder = new SassBuilder(new Compiler());
                    $cachePath = __DIR__ . "/../../../../.ntentan-build.cache";
                    Filesystem::directory($cachePath)->createIfNotExists();
                    $builder->setCache(new Cache(new FileCache($cachePath)));
                    return $builder;
                });
                AssetBuilder::register("copy", fn() => new CopyBuilder());
                require __DIR__ . '/../../../../bootstrap/assets.php';
            }
            $indexFile = __DIR__ . '/../../../../public/index.php';
            if (file_exists($indexFile)) {
                require $indexFile;
            }
            die();
        }
    }

    private function rebuildAssets() {
        $client = strtolower(filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH') ?? "");
        return file_exists(__DIR__ . '/../../../../bootstrap/assets.php')
            && !isset($this->config['disable-asset-builder'])
            && $client != 'xmlhttprequest';
    }

    public function exceptionHandler(Throwable $exception) {
        require "exception.php";
        die();
    }
};

// If we made it this far then yield so php handles the rest
return false;
