<?php

namespace ntentan\dev\testing;

use ntentan\Config;

/**
 * Description of DatabaseTestCase
 *
 * @author ekow
 */
abstract class DatabaseTestCase extends \PHPUnit_Extensions_Database_TestCase
{
    protected function getConnection()
    {
        $atiaa = \ntentan\atiaa\Driver::getConnection(Config::get('db'));
        return $this->createDefaultDBConnection($atiaa->getPDO(), Config::get('db')['dbname']);
    }
}
