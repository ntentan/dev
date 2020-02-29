<?php

use clearice\io\Io;
use ntentan\dev\tasks\Initialize;
use ntentan\honam\Templates;
use ntentan\utils\Input;

require "vendor/autoload.php";

$templateEngine = new Templates();
$templateEngine->prependPath(__DIR__ . "/views");

if(Input::exists(Input::POST, 'namespace')) {
   $initialize = new Initialize($templateEngine, new Io());
   $initialize->setOptions(['namespace' => Input::post('namespace'), 'name' => Input::post('name')]);
   $initialize->run();
   header("Location: /");
} else {
    echo $templateEngine->render("view", ['page' => 'welcome']);
}
