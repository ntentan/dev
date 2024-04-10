<?php
namespace ntentan\dev\assets;

class Asset
{
    private static array $registry;

    public static function register($name, $factory): void
    {
        self::$registry[$name] = $factory;
    }

    public static function __callStatic($method, $arguments)
    {
        //$builder = AssetPipeline::getContainer()->resolve("{$method}_builder");
        $builder = AssetPipeline::createBuilder($methed, $arguments[2] ?? []);
        $builder->setInputs(is_array($arguments[0]) ? $arguments[0] : [$arguments[0]]);
        if(isset($arguments[1])) {
            $builder->setOutputFile($arguments[1]);
        }
        return $builder;
    }
}