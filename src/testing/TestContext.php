<?php

namespace ntentan\dev\testing;

class TestContext
{
    private static string $namespace = 'app';

    public static function setNamespace(string $namespace): void
    {
        self::$namespace = $namespace;
    }

    public static function getNamespace(): string
    {
        return self::$namespace;
    }

}