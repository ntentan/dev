<?php
namespace ntentan\dev\assets;

class Asset
{
    public static function __callStatic($method, $arguments)
    {
        $builder = AssetPipeline::getContainer()->resolve("{$method}_builder");
        $builder->setInputs(is_array($arguments[0]) ? $arguments[0] : [$arguments[0]]);
        return $builder;
    }
}