<?php

namespace ntentan\dev\tests\lib;

use ntentan\dev\assets\AssetBuilder;
use ntentan\dev\assets\AssetPipeline;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

abstract class TempDirectoryTestCase extends TestCase
{
    protected string $tempDir;
    protected string $publicDir;
    protected string $assetsDir;
    protected string $cacheDir;
    protected string $pipelineFile;

    protected function setUp(): void
    {
        parent::setUp();
        ini_set('error_log', '/dev/null');

        $this->tempDir = sys_get_temp_dir() . '/ntentan_dev_test_' . uniqid('', true);
        $this->publicDir = $this->tempDir . '/public';
        $this->assetsDir = $this->tempDir . '/assets';
        $this->cacheDir = $this->tempDir . '/.ntentan-build';
        $this->pipelineFile = $this->tempDir . '/assets.php';

        mkdir($this->publicDir, 0755, true);
        mkdir($this->assetsDir, 0755, true);
        mkdir($this->cacheDir, 0755, true);
        file_put_contents($this->pipelineFile, "<?php\n");

        $this->resetAssetPipeline();
        $this->resetAssetBuilderRegistry();

        AssetPipeline::setup([
            'public-path' => $this->publicDir,
            'assets-path' => $this->assetsDir,
            'pipeline-path' => $this->pipelineFile,
            'force' => false,
        ]);
    }

    protected function tearDown(): void
    {
        $this->resetAssetPipeline();
        $this->resetAssetBuilderRegistry();
        $this->removeDirectory($this->tempDir);
        ini_set('error_log', '/dev/null');
        parent::tearDown();
    }

    protected function resetAssetPipeline(): void
    {
        $reflection = new ReflectionClass(AssetPipeline::class);
        $defined = $reflection->getProperty('defined');
        $defined->setValue(null, []);
    }

    protected function resetAssetBuilderRegistry(): void
    {
        $reflection = new ReflectionClass(AssetBuilder::class);
        $registry = $reflection->getProperty('registry');
        $registry->setValue(null, []);
    }

    protected function removeDirectory(string $dir): void
    {
        if (!file_exists($dir)) {
            return;
        }
        if (is_file($dir) || is_link($dir)) {
            unlink($dir);
            return;
        }
        $items = scandir($dir);
        if ($items === false) {
            return;
        }
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $this->removeDirectory($dir . DIRECTORY_SEPARATOR . $item);
        }
        rmdir($dir);
    }
}

