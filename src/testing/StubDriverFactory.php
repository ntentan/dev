<?php
namespace ntentan\dev\testing;

use ntentan\atiaa\Driver;
use ntentan\atiaa\DriverFactoryInterface;

class StubDriverFactory implements DriverFactoryInterface
{

    public function createDriver(): Driver
    {
        return new StubDriver([]);
    }
}
