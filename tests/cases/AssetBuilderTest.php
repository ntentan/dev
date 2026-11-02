<?php

namespace ntentan\dev\tests\cases;

use Exception;
use ntentan\dev\assets\AssetBuilder;
use ntentan\dev\assets\builders\ConcatBuilder;
use ntentan\dev\tests\lib\TempDirectoryTestCase;
use ReflectionClass;

class AssetBuilderTest extends TempDirectoryTestCase
{
    private function createDummyBuilder(): AssetBuilder
    {
        return new class extends AssetBuilder {
            public int $buildCount = 0;

            public function build(): void
            {
                $this->buildCount++;
                $outputFile = $this->getOutputFile();
                if ($outputFile !== null) {
                    $contents = [];
                    foreach ($this->expandInputs() as $file) {
                        $contents[] = file_get_contents($file);
                    }
                    file_put_contents($outputFile, implode('|', $contents));
                }
            }

            public function getDescription(): string
            {
                return 'dummy';
            }

            public function getExpandedInputs(): array
            {
                return $this->expandInputs();
            }
        };
    }

    public function testRegisterAndCreateBuilder(): void
    {
        AssetBuilder::register('concat', fn() => new ConcatBuilder());
        $builder = AssetBuilder::create('concat');

        $this->assertInstanceOf(ConcatBuilder::class, $builder);
        $builder->setOutputFile('out.txt');
    }

    public function testCreateThrowsExceptionForUnregisteredBuilder(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unknown asset builder nonexistent');

        AssetBuilder::create('nonexistent');
    }

    public function testSetAndGetOptions(): void
    {
        $builder = $this->createDummyBuilder();
        $builder->setOutputFile('out.txt');

        $this->assertSame([], $builder->getOptions());

        $options = ['include_paths' => ['/path/a', '/path/b']];
        $builder->setOptions($options);
        $this->assertSame($options, $builder->getOptions());
    }

    public function testGetOutputFileCreatesNestedDirectory(): void
    {
        $builder = $this->createDummyBuilder();
        $this->assertNull($builder->getOutputFile());

        $returned = $builder->setOutputFile('nested/dir/bundle.js');
        $this->assertSame($builder, $returned);

        $expectedPath = $this->publicDir . '/nested/dir/bundle.js';
        $this->assertSame($expectedPath, $builder->getOutputFile());
        $this->assertDirectoryExists(dirname($expectedPath));
    }

    public function testExpandInputsDirectAndFallbackToAssetsPath(): void
    {
        $directFile = $this->tempDir . '/direct.js';
        $assetFile1 = $this->assetsDir . '/sub/a.js';
        $assetFile2 = $this->assetsDir . '/sub/b.js';

        mkdir($this->assetsDir . '/sub', 0755, true);
        file_put_contents($directFile, 'direct');
        file_put_contents($assetFile1, 'a');
        file_put_contents($assetFile2, 'b');

        $logFile = $this->tempDir . '/error.log';
        ini_set('error_log', $logFile);

        $builder = $this->createDummyBuilder();
        $builder->setOutputFile('out.js');
        $builder->setInputs([
            $directFile,
            'sub/*.js',
            'missing/*.js',
        ]);

        $expanded = $builder->getExpandedInputs();
        $this->assertCount(3, $expanded);
        $this->assertContains(realpath($directFile), $expanded);
        $this->assertContains(realpath($assetFile1), $expanded);
        $this->assertContains(realpath($assetFile2), $expanded);

        // Subsequent calls use cached expandedInputs
        $this->assertSame($expanded, $builder->getExpandedInputs());

        $this->assertFileExists($logFile);
        $this->assertStringContainsString('Cannot find input file [missing/*.js]', file_get_contents($logFile));
    }

    public function testHasChangesLifecycle(): void
    {
        $inputFile = $this->assetsDir . '/source.js';
        file_put_contents($inputFile, 'v1');

        $builder = $this->createDummyBuilder();
        $builder->setInputs(['source.js']);
        $builder->setOutputFile('built.js');

        // Output file does not exist yet -> hasChanges() is true
        $this->assertTrue($builder->hasChanges());

        $builder->build();
        $outputFile = $builder->getOutputFile();
        $this->assertFileExists($outputFile);

        // Input is older than or equal to output -> hasChanges() is false
        touch($inputFile, 1000);
        touch($outputFile, 2000);
        clearstatcache();
        $this->assertFalse($builder->hasChanges());

        // Input modified after output -> hasChanges() is true
        touch($inputFile, 3000);
        clearstatcache();
        $this->assertTrue($builder->hasChanges());
    }

    public function testToStringBuildsAndReturnsOutputFile(): void
    {
        $inputFile = $this->assetsDir . '/hello.txt';
        file_put_contents($inputFile, 'hello world');

        $builder = $this->createDummyBuilder();
        $builder->setInputs([$inputFile]);
        $builder->setOutputFile('hello_out.txt');

        $outputPath = (string) $builder;

        $this->assertSame($this->publicDir . '/hello_out.txt', $outputPath);
        $this->assertSame(1, $builder->buildCount);
        $this->assertSame('hello world', file_get_contents($outputPath));
    }

    public function testDestructDeletesTemporaryOutputFileOnlyWhenTemp(): void
    {
        $inputFile = $this->assetsDir . '/temp_in.txt';
        file_put_contents($inputFile, 'data');

        // Builder 1: non-temp (setOutputFile sets isTemp = false) -> file persists on destruct
        $persistentBuilder = $this->createDummyBuilder();
        $persistentBuilder->setInputs([$inputFile]);
        $persistentBuilder->setOutputFile('persistent.txt');
        $persistentBuilder->build();
        $persistentPath = $persistentBuilder->getOutputFile();
        $this->assertFileExists($persistentPath);
        unset($persistentBuilder);
        $this->assertFileExists($persistentPath);

        // Builder 2: isTemp = true -> file is unlinked on destruct
        $tempBuilder = $this->createDummyBuilder();
        $tempBuilder->setInputs([$inputFile]);
        $tempBuilder->setOutputFile('temp_output.txt');
        $tempBuilder->build();
        $tempPath = $tempBuilder->getOutputFile();

        $reflection = new ReflectionClass(AssetBuilder::class);
        $isTempProp = $reflection->getProperty('isTemp');
        $isTempProp->setValue($tempBuilder, true);

        $this->assertFileExists($tempPath);
        unset($tempBuilder);
        $this->assertFileDoesNotExist($tempPath);
    }
}

