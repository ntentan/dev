<?php

namespace ntentan\dev\testing\nibii;

use ntentan\atiaa\Driver;

class MockDriver extends Driver
{
    private bool $connected = false;
    private array $queryLog = [];

    public function __construct(array $config = [])
    {
        parent::__construct(
            ['driver' => 'mock'],
            new class implements \ntentan\atiaa\DescriptorFactoryInterface {
                public function createDescriptor(\ntentan\atiaa\Driver $driver): \ntentan\atiaa\DescriptorInterface {
                    return new MockDescriptor();
                }
            }
        );
    }

    protected function getDriverName(): string
    {
        return 'mock';
    }

    public function quoteIdentifier($identifier)
    {
        return $identifier;
    }

    public function connect(): void
    {
        $this->connected = true;
    }

    public function query(string $query, array $bindData = null, bool $returnBoundData = false): array
    {
        $this->queryLog[] = ['query' => $query, 'bindData' => $bindData, 'returnBoundData' => $returnBoundData];
        return [];
    }

    public function beginTransaction()
    {

    }

    public function commit()
    {

    }

    public function getDefaultSchema(): string
    {
        return 'default_schema';
    }
}
