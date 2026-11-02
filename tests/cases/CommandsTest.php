<?php

namespace ntentan\dev\tests\cases;

use clearice\io\Io;
use ntentan\dev\commands\Build;
use ntentan\dev\commands\Debug;
use ntentan\dev\commands\Serve;
use ntentan\dev\tests\lib\TempDirectoryTestCase;
use ReflectionMethod;

class CommandsTest extends TempDirectoryTestCase
{
    public function testBuildCommandRunsAssetBuilderWhenPipelineFileExists(): void
    {
        $jsFile = $this->assetsDir . '/index.js';
        file_put_contents($jsFile, 'const ready = true;');

        $pipelineCode = <<<'PHP'
<?php
use ntentan\dev\assets\Asset;
use ntentan\dev\assets\AssetPipeline;

AssetPipeline::append(
    Asset::js('index.js', 'js/index.js')
);
PHP;
        file_put_contents($this->pipelineFile, $pipelineCode);

        $ioMock = $this->createMock(Io::class);
        $ioMock->expects($this->never())->method('error');

        $buildCommand = new Build($ioMock);
        $buildCommand->run([
            'pipeline-path' => $this->pipelineFile,
            'public-path' => $this->publicDir,
            'cache-path' => $this->cacheDir,
            'assets-path' => $this->assetsDir,
        ]);

        $this->assertFileExists($this->publicDir . '/js/index.js');
        $this->assertStringContainsString('const ready = true;', file_get_contents($this->publicDir . '/js/index.js'));
    }

    public function testBuildCommandExitsWithErrorCodeWhenPipelineFileMissing(): void
    {
        $missingPath = $this->tempDir . '/nonexistent_assets.php';
        $autoloadPath = realpath(__DIR__ . '/../../vendor/autoload.php');

        $script = sprintf(
            'require %s; '
            . '$io = new class extends \clearice\io\Io { public function error($string, $outputLevel = self::OUTPUT_LEVEL_1) { echo "ERR:" . $string; } }; '
            . '$cmd = new \ntentan\dev\commands\Build($io); '
            . '$cmd->run(["pipeline-path" => %s]);',
            var_export($autoloadPath, true),
            var_export($missingPath, true)
        );

        $output = [];
        $exitCode = 0;
        exec(escapeshellarg(PHP_BINARY) . ' -r ' . escapeshellarg($script), $output, $exitCode);

        $this->assertSame(1, $exitCode);
        $this->assertSame("ERR:Asset pipeline file not found: {$missingPath}", implode("\n", $output));
    }

    public function testServeBuildCommandConstructsServerCommandString(): void
    {
        $serve = new Serve();
        $buildCommandMethod = new ReflectionMethod(Serve::class, 'buildCommand');

        $command = $buildCommandMethod->invoke($serve, [
            'host' => '127.0.0.1',
            'port' => '8080',
        ]);

        $this->assertStringStartsWith(PHP_BINARY, $command);
        $this->assertStringContainsString('-d cli_server.color=1', $command);
        $this->assertStringContainsString('-S 127.0.0.1:8080', $command);
        $this->assertStringContainsString('src/router.php', $command);
    }

    public function testServeShutdownRemovesRuntimeConfigAndOutputsMessage(): void
    {
        $originalCwd = getcwd();
        chdir($this->tempDir);

        try {
            file_put_contents('.ntentan-dev.json', '{"host":"127.0.0.1","port":"8080"}');
            $this->assertFileExists('.ntentan-dev.json');

            $serve = new Serve();
            $shutdownMethod = new ReflectionMethod(Serve::class, 'shutdown');

            $this->expectOutputString("\nShutting down ... OK\n");
            $shutdownMethod->invoke($serve);

            $this->assertFileDoesNotExist('.ntentan-dev.json');
        } finally {
            chdir($originalCwd);
        }
    }

    public function testDebugCommandWithDumpRouterOptionEchoesCommandAndReturnsZero(): void
    {
        $debug = new Debug();

        ob_start();
        $exitCode = $debug->run([
            'host' => '0.0.0.0',
            'port' => '9000',
            'dump-router' => true,
        ]);
        $output = ob_get_clean();

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('-dxdebug.mode=debug', $output);
        $this->assertStringContainsString('-dxdebug.client_port=9003', $output);
        $this->assertStringContainsString('-dxdebug.client_host=127.0.0.1', $output);
        $this->assertStringContainsString('-S 0.0.0.0:9000', $output);
        $this->assertStringContainsString('src/router.php', $output);
    }
}
