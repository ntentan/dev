<?php
$requestUri = filter_input(INPUT_SERVER, 'REQUEST_URI');
if(is_file(getcwd() . $requestUri) || $requestUri == '/favicon.ico' ) {
    return false;
} else {
    set_exception_handler('exception_handler');
    set_error_handler('error_handler');
    require 'index.php';
}

function exception_handler($exception)
{
    var_dump($exception);
}

function error_handler($error)
{
    var_dump($error);
}
