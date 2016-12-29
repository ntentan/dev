<?php

use ntentan\honam\TemplateEngine;

new class 
{    
    private $config;
    
    public function __construct() {
        $this->config = json_decode(file_get_contents('~ntentan.dev.config.json'), true);
        $requestUri = filter_input(INPUT_SERVER, 'REQUEST_URI');
        if(is_file(getcwd() . $requestUri) || $requestUri == '/favicon.ico' ) {
            return false;
        } else {
            set_exception_handler([$this, 'exceptionHandler']);
            //set_error_handler([$this, 'errorHandler']);
            require 'index.php';
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
        print TemplateEngine::render(
            'exception', 
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
    
    public function errorHandler($error) {
        //var_dump($error);
        //$this->displayMessage();
    }
};
