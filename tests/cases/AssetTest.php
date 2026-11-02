<?php

namespace ntentan\dev\tests\cases;

use Exception;
use ntentan\dev\assets\Asset;
use ntentan\dev\assets\AssetBuilder;
use ntentan\dev\assets\builders\ConcatBuilder;
use ntentan\dev\assets\builders\CopyBuilder;
use ntentan\dev\assets\builders\JsBuilder;
use ntentan\dev\tests\lib\TempDirectoryTestCase;
use ReflectionClass;

class AssetTest extends TempDirectoryTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        AssetBuilder::register('js', fn() => new JsBuilder());
        AssetBuilder::register('copy', fn() => new CopyBuilder());
        AssetBuilder::register('concat', fn() => new ConcatBuilder());
    }

    public function testCallStaticWithSingleStringInput(): void
    {
        $inputFile = $this->assetsDir . '/app.js';
        file_put_contents($inputFile, 'console.log("app");');

        $builder = Asset::js($inputFile, 'js/bundle.js');

        $this->assertInstanceOf(JsBuilder::class, $builder);
        $this->assertSame($this->publicDir . '/js/bundle.js', $builder->getOutputFile());

        $reflection = new ReflectionClass(AssetBuilder::class);
        $inputsProp = $reflection->getProperty('inputs');
        $this->assertSame([$inputFile], $inputsProp->getValue($builder));
    }

    public function testCallStaticWithArrayInputAndOptions(): void
    {
        $inputs = [$this->assetsDir . '/one.js', $this->assetsDir . '/two.js'];
        $options = ['minify' => true, 'sourcemap' => false];

        $builder = Asset::concat($inputs, 'js/all.js', $options);

        $this->assertInstanceOf(ConcatBuilder::class, $builder);
        $this->assertSame($this->publicDir . '/js/all.js', $builder->getOutputFile());
        $this->assertSame($options, $builder->getOptions());

        $reflection = new ReflectionClass(AssetBuilder::class);
        $inputsProp = $reflection->getProperty('inputs');
        $this->assertSame($inputs, $inputsProp->getValue($builder));
    }

    public function testCallStaticWithoutOutputFile(): void
    {
        $builder = Asset::copy('images/*.png');

        $this->assertInstanceOf(CopyBuilder::class, $builder);
        $this->assertNull($builder->getOutputFile());

        // Set output file before destruction so __destruct does not pass null to file_exists()
        $builder->setOutputFile('unused.txt');
    }

    public function testCallStaticThrowsOnUnknownBuilder(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unknown asset builder unknown_builder');

        Asset::unknown_builder('input.txt', 'output.txt');
    }
}

