<?php
namespace ntentan\dev\assets;

class Asset
{
    public static function __callStatic($method, $arguments)
    {
        $builder = AssetBuilder::create($method);
        $builder->setInputs(is_array($arguments[0]) ? $arguments[0] : [$arguments[0]]);
        if(isset($arguments[1])) {
            $builder->setOutputFile($arguments[1]);
        }
        if(isset($arguments[2])) {
            $builder->setOptions($arguments[2]);
        }
        return $builder;
    }
}