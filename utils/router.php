<?php
$requestUri = filter_input(INPUT_SERVER, 'REQUEST_URI');
if(is_file(getcwd() . $requestUri) || $requestUri == '/favicon.ico' ) {
    return false;
} else {
    require 'index.php';
}