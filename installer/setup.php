<?php

use ntentan\honam\Templates;

require "vendor/autoload.php";

$templateEngine = new Templates();
$templateEngine->prependPath(__DIR__ . "/views");
echo $templateEngine->render("view", ['page' => 'welcome']);

