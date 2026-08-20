<?php

namespace ntentan\dev\testing;

use ntentan\atiaa\DbContext;
use ntentan\kaikai\Cache;
use ntentan\nibii\DriverAdapter;
use ntentan\nibii\factories\DefaultModelFactory;
use ntentan\nibii\factories\DriverAdapterFactory;
use ntentan\nibii\interfaces\ModelFactoryInterface;
use ntentan\nibii\interfaces\ValidatorFactoryInterface;
use ntentan\nibii\ORMContext;

class ModelMock
{
    public static function initialize(): void
    {
        $modelFactory = new DefaultModelFactory();
        $driverAdapterFactory = \Mockery::mock(DriverAdapterFactory::class);
        $driverAdapterFactory->shouldReceive('createDriverAdapter')->andReturn(
            \Mockery::mock(DriverAdapter::class)
        );
        $validatorFactory = \Mockery::mock(ValidatorFactoryInterface::class);
        $cache = \Mockery::mock(Cache::class);

        DbContext::initialize(new Driver);
        ORMContext::initialize($modelFactory, $driverAdapterFactory, $validatorFactory, $cache);
    }

    public static function mockStatic(string $className)
    {
        return \Mockery::mock($className);
    }

    public static function mock(string $className)
    {
        return \Mockery::mock($className);
    }
}