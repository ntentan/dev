<?php

namespace ntentan\dev;

use ntentan\utils\Filesystem;

abstract class TaskRunner 
{
    abstract public function runTask($params);
    
    public function writeFile($templateFile, $data, $destination)
    {
        $output = \ntentan\honam\TemplateEngine::renderString(
            Filesystem::get($templateFile)->getContents(), 
            'mustache', $data
        );
        Filesystem::get($destination)->putContents($output);
    }
}
