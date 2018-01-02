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
            if(file_exists('asset_pipeline.php') && file_exists('public') && !isset($this->config['disable-asset-builder'])){
                AssetPipeline::setup(['public-dir' => 'public', 'asset-pipeline' => 'asset_pipeline.php']);
                require 'asset_pipeline.php';
            }
            require 'index.php';
            die();
        }
    }
    
    /**
     * 
     * @param \Exception $exception
     */
    public function displayMessage($exception) {
        ob_clean();
        $reflection = new ReflectionClass($exception);
        TemplateEngine::prependPath(__DIR__ . '/../templates/pages');
        $template = 'exception';
        http_response_code(500);
        foreach (headers_list() as $header) {
            preg_match("/(?<header>.*):(?<value>.*)/", $header, $matches);
            if($matches['header'] == 'Content-Type') {
                if(trim($matches['value']) != 'text/html') {
                    $template = 'plain-exception';
                }
                break;
            } 
        }
        print TemplateEngine::render(
            $template, 
            [
                "type" => $reflection->getName(),
                "message" => $exception->getMessage(),
                "stack_trace" => $exception->getTrace(),
                "line" => $exception->getLine(),
                "file" => $exception->getFile()
            ]
        );
    }
    
    public function exceptionHandler($exception) {
        $this->displayMessage($exception);
        die();
    }
};

// If we made it this far then yield so php handles the rest
return false;