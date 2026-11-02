<?php

namespace ntentan\dev\tests\cases;

use ntentan\dev\assets\Asset;
use ntentan\dev\assets\AssetBuilder;
use ntentan\dev\assets\AssetPipeline;
use ntentan\dev\assets\builders\ConcatBuilder;
use ntentan\dev\tests\lib\TempDirectoryTestCase;

require_once __DIR__ . '/../../src/builder.php';

class AssetPipelineTest extends TempDirectoryTestCase
{
    public function testSetupAndPathGetters(): void
    {
        AssetPipeline::setup([
            'public-path' => '/var/www/public',
            'assets-path' => '/var/www/assets',
            'pipeline-path' => '/var/www/assets.php',
            'force' => true,
        ]);

        $this->assertSame('/var/www/public', AssetPipeline::getPublicPath());
        $this->assertSame('/var/www/assets', AssetPipeline::getAssetsPath());
    }

    public function testAppendAndRunBuildsModifiedAssetsAndSkipsUnmodified(): void
    {
        $inputFile = $this->assetsDir . '/script.js';
        file_put_contents($inputFile, 'const a = 1;');

        touch($this->pipelineFile, 1000);
        touch($inputFile, 1500);

        $builder = new ConcatBuilder();
        $builder->setInputs([$inputFile]);
        $builder->setOutputFile('js/script.js');

        AssetPipeline::append($builder);
        AssetPipeline::run();

        $outputFile = $builder->getOutputFile();
        $this->assertFileExists($outputFile);
        $this->assertStringContainsString('const a = 1;', file_get_contents($outputFile));

        // Set output file timestamp newer than both input and pipeline file, and overwrite content to detect rebuilds
        file_put_contents($outputFile, 'CACHED_CONTENT');
        touch($outputFile, 2000);
        clearstatcache();

        AssetPipeline::run();
        $this->assertSame('CACHED_CONTENT', file_get_contents($outputFile));

        // Touch pipeline file to be newer than output file -> triggers rebuild
        touch($this->pipelineFile, 3000);
        clearstatcache();

        AssetPipeline::run();
        $this->assertStringContainsString('const a = 1;', file_get_contents($outputFile));
    }

    public function testRunWithForceRebuildOption(): void
    {
        $inputFile = $this->assetsDir . '/style.css';
        file_put_contents($inputFile, 'body { margin: 0; }');

        $builder = new ConcatBuilder();
        $builder->setInputs([$inputFile]);
        $builder->setOutputFile('css/style.css');
        $outputFile = $builder->getOutputFile();
        file_put_contents($outputFile, 'OLD_CONTENT');

        touch($this->pipelineFile, 1000);
        touch($inputFile, 1000);
        touch($outputFile, 2000);
        clearstatcache();

        AssetPipeline::setup([
            'public-path' => $this->publicDir,
            'assets-path' => $this->assetsDir,
            'pipeline-path' => $this->pipelineFile,
            'force' => true,
        ]);

        AssetPipeline::append($builder);
        AssetPipeline::run();

        $this->assertStringContainsString('body { margin: 0; }', file_get_contents($outputFile));
    }

    public function testRunSkipsBuilderWithoutOutputFileAndLogsWarning(): void
    {
        $logFile = $this->tempDir . '/pipeline.log';
        ini_set('error_log', $logFile);

        $builder = new ConcatBuilder();
        $builder->setInputs([]);

        AssetPipeline::append($builder);
        AssetPipeline::run();

        $this->assertFileExists($logFile);
        $this->assertStringContainsString('No output path specified for concatenator', file_get_contents($logFile));

        // Prevent null filename deprecation on __destruct
        $builder->setOutputFile('unused.txt');
    }

    public function testRunAssetBuilderFunctionRegistersBuildersAndExecutesPipeline(): void
    {
        $jsInput = $this->assetsDir . '/app.js';
        $scssInput = $this->assetsDir . '/main.scss';
        $copyInput = $this->assetsDir . '/logo.svg';

        file_put_contents($jsInput, 'console.log("loaded");');
        file_put_contents($scssInput, '$primary: #336699; .btn { color: $primary; }');
        file_put_contents($copyInput, '<svg></svg>');

        $pipelineCode = <<<'PHP'
<?php
use ntentan\dev\assets\Asset;
use ntentan\dev\assets\AssetPipeline;

AssetPipeline::append(
    Asset::js('app.js', 'js/app.js'),
    Asset::sass('main.scss', 'css/main.css'),
    Asset::copy('logo.svg', 'images/')
);
PHP;
        file_put_contents($this->pipelineFile, $pipelineCode);

        runAssetBuilder([
            'pipeline-path' => $this->pipelineFile,
            'public-path' => $this->publicDir,
            'cache-path' => $this->cacheDir,
            'assets-path' => $this->assetsDir,
        ]);

        $this->assertFileExists($this->publicDir . '/js/app.js');
        $this->assertStringContainsString('console.log("loaded");', file_get_contents($this->publicDir . '/js/app.js'));

        $this->assertFileExists($this->publicDir . '/css/main.css');
        $this->assertStringContainsString('#336699', file_get_contents($this->publicDir . '/css/main.css'));

        $this->assertFileExists($this->publicDir . '/images/logo.svg');
        $this->assertSame('<svg></svg>', file_get_contents($this->publicDir . '/images/logo.svg'));
    }
}

