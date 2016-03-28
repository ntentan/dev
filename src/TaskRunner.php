<?php

namespace ntentan\dev;

use ntentan\utils\Filesystem;

abstract class TaskRunner 
{
    abstract public function runTask($params);
    
    public function writeFile($templateFile, $data, $destination, $prefix = '')
    {
        $output = \ntentan\honam\TemplateEngine::renderString(
            Filesystem::get($templateFile)->getContents(), 
            'php', $data
        );
        Filesystem::get($destination)->putContents($prefix . $output);
    }
}
