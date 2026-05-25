<?php

namespace ntentan\dev\testing\nibii;

use ntentan\atiaa\DescriptorInterface;

class MockDescriptor implements DescriptorInterface
{
    public function describe()
    {
        return [];
    }

    public function describeTables($schema, $requestedTables = [], $includeViews = false)
    {
        return [];
    }

    public function setCleanDefaults($cleanDefaults)
    {

    }
}
