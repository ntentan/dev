<?php

namespace ntentan\dev\tests\cases;

use Exception;
use Mockery;
use ntentan\atiaa\DbContext;
use ntentan\dev\testing\ModelFactories;
use ntentan\dev\testing\ModelMock;
use ntentan\dev\testing\StubDriver;
use ntentan\dev\testing\StubDriverAdapter;
use ntentan\dev\testing\StubDriverAdapterFactory;
use ntentan\dev\testing\StubDriverFactory;
use ntentan\dev\testing\StubModelFactory;
use ntentan\dev\testing\TestContext;
use ntentan\dev\testing\TestingModelFactory;
use ntentan\nibii\factories\DefaultModelFactory;
use ntentan\nibii\interfaces\ModelFactoryInterface;
use ntentan\nibii\ORMContext;
use ntentan\nibii\RecordWrapper;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class TestingInfrastructureTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        TestContext::setNamespace('app');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        TestContext::setNamespace('app');
        parent::tearDown();
    }

    public function testTestContextGetAndSetNamespace(): void
    {
        $this->assertSame('app', TestContext::getNamespace());

        TestContext::setNamespace('custom\\ns');
        $this->assertSame('custom\\ns', TestContext::getNamespace());
    }

    public function testModelFactoriesEnumValues(): void
    {
        $this->assertSame(DefaultModelFactory::class, ModelFactories::DEFAULT->value);
        $this->assertSame('ntentan\\dev\\testing\\MockMvcModelFactory', ModelFactories::MVC->value);
    }

    public function testStubDriverAndStubDriverFactory(): void
    {
        $factory = new StubDriverFactory();
        $driver = $factory->createDriver();

        $this->assertInstanceOf(StubDriver::class, $driver);
        $this->assertSame('users_table', $driver->quoteIdentifier('users_table'));

        $getDriverName = new ReflectionMethod(StubDriver::class, 'getDriverName');
        $this->assertSame('testing', $getDriverName->invoke($driver));
    }

    public function testStubDriverAdapterAndFactory(): void
    {
        $factory = new StubDriverAdapterFactory();
        $adapter = $factory->createDriverAdapter();

        $this->assertInstanceOf(StubDriverAdapter::class, $adapter);
        $this->assertNull($adapter->mapDataTypes('varchar'));
    }

    public function testStubModelFactoryImplementsModelFactoryInterface(): void
    {
        $factory = new StubModelFactory();
        $this->assertInstanceOf(ModelFactoryInterface::class, $factory);
    }

    public function testTestingModelFactoryRegistersAndLoadsModelsInLifoOrder(): void
    {
        $factory = new TestingModelFactory('customapp');
        $model1 = new RecordWrapper();
        $model2 = new RecordWrapper();

        $factory->registerCall('users', $model1);
        $factory->registerCall('users', $model2);

        $this->assertSame($model2, $factory->loadRecordWrapper('users'));
        $this->assertSame($model1, $factory->loadRecordWrapper('users'));
    }

    public function testTestingModelFactoryThrowsExceptionOnUnexpectedLoad(): void
    {
        $factory = new TestingModelFactory('app');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Not expecting a call to posts.');

        $factory->loadRecordWrapper('posts');
    }

    public function testModelMockInitializeWithNullUsesDefaultModelFactory(): void
    {
        ModelMock::initialize();

        $this->assertInstanceOf(DefaultModelFactory::class, ORMContext::getModelFactory());
        $this->assertInstanceOf(StubDriverAdapterFactory::class, ORMContext::getDriverAdapterFactory());
        $this->assertInstanceOf(StubDriverFactory::class, DbContext::getDriverFactory());
    }

    public function testModelMockInitializeWithModelFactoriesEnum(): void
    {
        ModelMock::initialize(ModelFactories::DEFAULT);
        $this->assertInstanceOf(DefaultModelFactory::class, ORMContext::getModelFactory());

        TestContext::setNamespace('acme');
        ModelMock::initialize(ModelFactories::MVC);
        $mvcFactory = ORMContext::getModelFactory();
        $this->assertInstanceOf(TestingModelFactory::class, $mvcFactory);
    }

    public function testModelMockInitializeWithCustomModelFactoryInstance(): void
    {
        $customFactory = new TestingModelFactory('custom');
        ModelMock::initialize($customFactory);

        $this->assertSame($customFactory, ORMContext::getModelFactory());
    }

    public function testModelMockStaticCallWithProvidedInstance(): void
    {
        $factory = new TestingModelFactory('app');
        ModelMock::initialize($factory);

        $instance = new RecordWrapper();
        $returned = ModelMock::staticCall('User', $instance);

        $this->assertSame($instance, $returned);
        $this->assertSame($instance, $factory->loadRecordWrapper('User'));
    }

    public function testModelMockStaticCallCreatesMockeryMockForMvcModelFactory(): void
    {
        $factory = new TestingModelFactory('app');
        ModelMock::initialize($factory);

        $mock = ModelMock::staticCall(RecordWrapper::class);

        $this->assertInstanceOf(RecordWrapper::class, $mock);
        $this->assertSame($mock, $factory->loadRecordWrapper(RecordWrapper::class));
    }

    public function testModelMockStaticCallTriggersNoticeOnNonMvcModelFactory(): void
    {
        ModelMock::initialize(ModelFactories::DEFAULT);

        $triggeredMessage = null;
        set_error_handler(function (int $errno, string $errstr) use (&$triggeredMessage): bool {
            $triggeredMessage = $errstr;
            return true;
        });

        try {
            $mock = ModelMock::staticCall(RecordWrapper::class);
            $this->assertInstanceOf(RecordWrapper::class, $mock);
            $this->assertSame('Cannot call static methods on non MvcModelFactories', $triggeredMessage);
        } finally {
            restore_error_handler();
        }
    }
}

