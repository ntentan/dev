<?php

namespace ntentan\dev\tests\cases;

use ntentan\dev\assets\builders\SassBuilder;
use ntentan\dev\tests\lib\TempDirectoryTestCase;
use ReflectionMethod;
use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\Logger\QuietLogger;

class SassBuilderTest extends TempDirectoryTestCase
{
    private function createCompiler(): Compiler
    {
        $compiler = new Compiler();
        $compiler->setLogger(new QuietLogger());
        return $compiler;
    }

    public function testGetDescription(): void
    {
        $builder = new SassBuilder($this->createCompiler());
        $builder->setOutputFile('css/app.css');
        $this->assertSame('scss', $builder->getDescription());
    }

    public function testBuildCompilesScssWithPartialsIncludePathsAndCssImports(): void
    {
        $scssDir = $this->assetsDir . '/scss';
        $sharedDir = $this->assetsDir . '/shared_scss';
        mkdir($scssDir, 0755, true);
        mkdir($sharedDir, 0755, true);

        // Partial in the same directory (_variables.scss) imported without extension
        file_put_contents($scssDir . '/_variables.scss', '$brand-color: #ff6600;');
        // File in include_paths imported with explicit .scss extension
        file_put_contents($sharedDir . '/mixins.scss', '.shared-box { display: flex; }');

        // Main entry file with @import for partial, @import with .scss extension, and CSS @import
        $mainScss = <<<'SCSS'
@import "https://example.com/external.css";
@import "variables";
@import "mixins.scss";

body {
    color: $brand-color;
}
SCSS;
        $mainFile = $scssDir . '/main.scss';
        file_put_contents($mainFile, $mainScss);

        $builder = new SassBuilder($this->createCompiler());
        $builder->setCachePath($this->cacheDir);
        $builder->setInputs([$mainFile]);
        $builder->setOutputFile('css/main.css');
        $builder->setOptions(['include_paths' => [$sharedDir]]);

        // Before build, cache file does not exist -> hasChanges() returns true
        $this->assertTrue($builder->hasChanges());

        $builder->build();

        $outputFile = $builder->getOutputFile();
        $this->assertFileExists($outputFile);
        $css = file_get_contents($outputFile);

        $this->assertStringContainsString('@import "https://example.com/external.css";', $css);
        $this->assertStringContainsString('#ff6600', $css);
        $this->assertStringContainsString('.shared-box', $css);

        // Verify .scssbuild cache file was created and contains touched files
        $expectedCacheFile = sprintf('%s/%s.scssbuild', $this->cacheDir, md5($outputFile));
        $this->assertFileExists($expectedCacheFile);
        $touchedFiles = json_decode(file_get_contents($expectedCacheFile), true);
        $this->assertIsArray($touchedFiles);
        $this->assertContains(realpath($mainFile), $touchedFiles);
        $this->assertContains('file://' . realpath($scssDir . '/_variables.scss'), $touchedFiles);
        $this->assertContains('file://' . realpath($sharedDir . '/mixins.scss'), $touchedFiles);
    }

    public function testHasChangesDetectsModifiedIncludedFiles(): void
    {
        $partialFile = $this->assetsDir . '/_colors.scss';
        $mainFile = $this->assetsDir . '/style.scss';

        file_put_contents($partialFile, '$bg: #000;');
        file_put_contents($mainFile, '@import "colors"; .page { background: $bg; }');

        $builder = new SassBuilder($this->createCompiler());
        $builder->setCachePath($this->cacheDir);
        $builder->setInputs([$mainFile]);
        $builder->setOutputFile('css/style.css');

        $builder->build();
        $outputFile = $builder->getOutputFile();

        // Make output file newer than both main and partial files
        touch($partialFile, 1000);
        touch($mainFile, 1000);
        touch($outputFile, 2000);
        clearstatcache();

        $this->assertFalse($builder->hasChanges());

        // Modify only the imported partial file -> hasChanges() detects it via .scssbuild cache
        $logFile = $this->tempDir . '/sass.log';
        ini_set('error_log', $logFile);

        touch($partialFile, 3000);
        clearstatcache();

        $this->assertTrue($builder->hasChanges());
        $this->assertFileExists($logFile);
        $this->assertStringContainsString('modified ...', file_get_contents($logFile));
    }

    public function testFindPathReturnsNullWhenFileNotFound(): void
    {
        $builder = new SassBuilder($this->createCompiler());
        $builder->setOutputFile('css/out.css');

        $findPathMethod = new ReflectionMethod(SassBuilder::class, 'findPath');
        $result = $findPathMethod->invoke($builder, [$this->assetsDir], 'nonexistent_partial');

        $this->assertNull($result);
    }
}
