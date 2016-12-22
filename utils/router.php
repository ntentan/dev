<?php
$requestUri = filter_input(INPUT_SERVER, 'REQUEST_URI');
$config = json_decode(file_get_contents('~ntentan.dev.config.json'), true);

if(is_file(getcwd() . $requestUri) || $requestUri == '/favicon.ico' ) {
    return false;
} else {
    set_exception_handler('exception_handler');
    set_error_handler('error_handler');
    require 'index.php';
}

function display_message($message)
{
    if($config['break-points'] && function_exists('xdebug_break')) {
        var_dump('braking ...');
        xdebug_break();
    }
}

function exception_handler($exception)
{
    var_dump('called ex...');
    display_message($exception->getMessage());
}

function error_handler($error)
{
    var_dump('called er...');
    display_message($exception->getMessage());
}
