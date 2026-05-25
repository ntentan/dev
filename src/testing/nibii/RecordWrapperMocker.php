<?php

namespace ntentan\dev\testing\nibii;

use ntentan\kaikai\backends\VolatileCache;
use ntentan\kaikai\Cache;
use ntentan\nibii\DriverAdapter;
use ntentan\atiaa\DriverFactory;
use ntentan\nibii\factories\DefaultModelFactory;
use ntentan\nibii\interfaces\DriverAdapterFactoryInterface;
use ntentan\nibii\interfaces\ModelFactoryInterface;
use ntentan\nibii\interfaces\ValidatorFactoryInterface;
use ntentan\nibii\ORMContext;
use ntentan\atiaa\DbContext;
use ntentan\atiaa\Driver;

class RecordWrapperMocker
{
    private static function createDriverAdapter(): DriverAdapterFactoryInterface
    {
        $driverAdapter = \Mockery::mock(DriverAdapterFactoryInterface::class);
        $driverAdapter->allows()->createDriverAdapter()->andReturns(new MockAdapter());
        return $driverAdapter;
    }

    private static function createDriverFactory(): DriverFactory
    {
        $driverFactory = \Mockery::mock(DriverFactory::class);
        $driverFactory->allows()->createDriver()->andReturns(new MockDriver());
        return $driverFactory;
    }

    public static function initialize(?ModelFactoryInterface $modelFactory=null): void
    {
        $modelFactory = $modelFactory ?? new DefaultModelFactory();
        $validatorFactory = \Mockery::mock(ValidatorFactoryInterface::class);
        $driverAdapter = self::createDriverAdapter();
        $driverFactory = self::createDriverFactory();
        $cache = new Cache(new VolatileCache());

        ORMContext::initialize($modelFactory, $driverAdapter, $validatorFactory, $cache);
        DbContext::initialize($driverFactory);
    }
}
