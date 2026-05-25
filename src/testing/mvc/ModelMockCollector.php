<?php

namespace ntentan\dev\testing\mvc;

class ModelMockCollector
{
    private $mock;
    public function willReturn($mock)
    {
        $this->mock = $mock;
    }

    public function getMock()
    {
        return $this->mock;
    }
}
