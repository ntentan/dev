<?php

namespace ntentan\dev\testing;

use ntentan\atiaa\Driver;

class StubDriver extends Driver
{

    protected function getDriverName()
    {
        return "testing";
    }

    public function quoteIdentifier($identifier)
    {
        return $identifier;
    }
}

