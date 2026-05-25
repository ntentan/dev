<?php

namespace ntentan\dev\testing\nibii;

use ntentan\nibii\DriverAdapter;

class MockAdapter extends DriverAdapter
{
    public function mapDataTypes($nativeType): string
    {
        return $nativeType;
    }
}
