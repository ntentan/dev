<?php

namespace ntentan\atiaa {
    if (!class_exists(Driver::class)) {
        abstract class Driver
        {
            protected array $config;

            public function __construct(array $config = [])
            {
                $this->config = $config;
            }

            abstract protected function getDriverName();

            abstract public function quoteIdentifier($identifier);
        }
    }

    if (!interface_exists(DriverFactoryInterface::class)) {
        interface DriverFactoryInterface
        {
            public function createDriver(): Driver;
        }
    }

    if (!class_exists(DbContext::class)) {
        class DbContext
        {
            private static ?DriverFactoryInterface $driverFactory = null;

            public static function initialize(DriverFactoryInterface $driverFactory): void
            {
                self::$driverFactory = $driverFactory;
            }

            public static function getDriverFactory(): ?DriverFactoryInterface
            {
                return self::$driverFactory;
            }
        }
    }
}

namespace ntentan\nibii\relationships {
    if (!enum_exists(RelationshipType::class)) {
        enum RelationshipType
        {
            case BELONGS_TO;
            case HAS_MANY;
            case MANY_TO_MANY;
        }
    }
}

namespace ntentan\nibii {
    if (!class_exists(DriverAdapter::class)) {
        abstract class DriverAdapter
        {
            abstract public function mapDataTypes($nativeType);
        }
    }

    if (!class_exists(RecordWrapper::class)) {
        class RecordWrapper
        {
        }
    }
}

namespace ntentan\nibii\interfaces {
    use ntentan\nibii\DriverAdapter;
    use ntentan\nibii\RecordWrapper;
    use ntentan\nibii\relationships\RelationshipType;

    if (!interface_exists(DriverAdapterFactoryInterface::class)) {
        interface DriverAdapterFactoryInterface
        {
            public function createDriverAdapter(): DriverAdapter;
        }
    }

    if (!interface_exists(ModelFactoryInterface::class)) {
        interface ModelFactoryInterface
        {
            public function createModel(string $name, RelationshipType $context): RecordWrapper;
            public function getModelTable(RecordWrapper $instance): string;
            public function getClassName(string $model): string;
            public function getJunctionClassName(string $classA, string $classB): string;
        }
    }
}

namespace ntentan\nibii\factories {
    use ntentan\nibii\interfaces\ModelFactoryInterface;
    use ntentan\nibii\RecordWrapper;
    use ntentan\nibii\relationships\RelationshipType;

    if (!class_exists(DefaultModelFactory::class)) {
        class DefaultModelFactory implements ModelFactoryInterface
        {
            private array $registeredCalls = [];

            public function registerCall(string $name, mixed $instance): void
            {
                $this->registeredCalls[$name][] = $instance;
            }

            public function getRegisteredCalls(): array
            {
                return $this->registeredCalls;
            }

            public function createModel(string $name, RelationshipType $context): RecordWrapper
            {
                return new RecordWrapper();
            }

            public function getModelTable(RecordWrapper $instance): string
            {
                return 'default_table';
            }

            public function getClassName(string $model): string
            {
                return $model;
            }

            public function getJunctionClassName(string $classA, string $classB): string
            {
                return "{$classA}_{$classB}";
            }
        }
    }

    if (!class_exists(DefaultValidatorFactory::class)) {
        class DefaultValidatorFactory
        {
        }
    }
}

namespace ntentan\nibii {
    use ntentan\kaikai\Cache;
    use ntentan\nibii\interfaces\DriverAdapterFactoryInterface;
    use ntentan\nibii\interfaces\ModelFactoryInterface;

    if (!class_exists(ORMContext::class)) {
        class ORMContext
        {
            private static ?ModelFactoryInterface $modelFactory = null;
            private static ?DriverAdapterFactoryInterface $driverAdapterFactory = null;
            private static mixed $validatorFactory = null;
            private static ?Cache $cache = null;

            public static function initialize(
                ModelFactoryInterface $modelFactory,
                DriverAdapterFactoryInterface $driverAdapterFactory,
                mixed $validatorFactory,
                Cache $cache
            ): void {
                self::$modelFactory = $modelFactory;
                self::$driverAdapterFactory = $driverAdapterFactory;
                self::$validatorFactory = $validatorFactory;
                self::$cache = $cache;
            }

            public static function getModelFactory(): ?ModelFactoryInterface
            {
                return self::$modelFactory;
            }

            public static function getDriverAdapterFactory(): ?DriverAdapterFactoryInterface
            {
                return self::$driverAdapterFactory;
            }
        }
    }
}

namespace ntentan\mvc {
    use ntentan\nibii\interfaces\ModelFactoryInterface;
    use ntentan\nibii\RecordWrapper;
    use ntentan\nibii\relationships\RelationshipType;

    if (!class_exists(MvcModelFactory::class)) {
        class MvcModelFactory implements ModelFactoryInterface
        {
            protected string $namespace;

            public function __construct(string $namespace = 'app')
            {
                $this->namespace = $namespace;
            }

            public function getNamespace(): string
            {
                return $this->namespace;
            }

            public function createModel(string $name, RelationshipType $context): RecordWrapper
            {
                return new RecordWrapper();
            }

            public function getModelTable(RecordWrapper $instance): string
            {
                return 'mvc_table';
            }

            public function getClassName(string $model): string
            {
                return "{$this->namespace}\\models\\{$model}";
            }

            public function getJunctionClassName(string $classA, string $classB): string
            {
                return "{$classA}_{$classB}";
            }
        }
    }
}

