<?php

namespace ntentan\dev\testing;

use ntentan\nibii\DriverAdapter;
use ntentan\nibii\interfaces\DriverAdapterFactoryInterface;

class StubDriverAdapterFactory implements DriverAdapterFactoryInterface
{
    public function createDriverAdapter(): DriverAdapter
    {
        return new StubDriverAdapter();
    }
}