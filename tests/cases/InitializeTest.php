<?php

namespace ntentan\dev\tests\cases;

use clearice\io\Io;
use ntentan\dev\tasks\Initialize;
use ntentan\dev\tests\lib\TempDirectoryTestCase;
use ntentan\honam\Templates;

class InitializeTest extends TempDirectoryTestCase
{
    private string $originalCwd;
    private string $workDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalCwd = getcwd();
        $this->workDir = $this->tempDir . '/new_app';
        mkdir($this->workDir, 0755, true);
        chdir($this->workDir);
    }

    protected function tearDown(): void
    {
        chdir($this->originalCwd);
        parent::tearDown();
    }

    public function testRunPreflightFailsWhenDirectoriesAlreadyExist(): void
    {
        mkdir($this->workDir . '/public', 0755, true);
        mkdir($this->workDir . '/src', 0755, true);

        $templates = Templates::getDefaultInstance();
        $ioMock = $this->createMock(Io::class);
        $ioMock->expects($this->never())->method('output');

        $initialize = new Initialize($templates, $ioMock);
        $result = $initialize->run();

        $this->assertFalse($result['success']);
        $this->assertTrue($result['results']['home_writeable']);
        $this->assertFalse($result['results']['no_public_directory']);
        $this->assertTrue($result['results']['no_config_directory']);
        $this->assertFalse($result['results']['no_src_directory']);
        $this->assertTrue($result['results']['no_view_directory']);
    }

    public function testRunCreatesApplicationSkeletonWithCustomOptions(): void
    {
        $templates = Templates::getDefaultInstance();
        $ioMock = $this->createMock(Io::class);
        $ioMock->expects($this->exactly(2))->method('output');

        $initialize = new Initialize($templates, $ioMock);
        $initialize->setOptions([
            'namespace' => 'mycustomapp',
            'name' => 'My Custom App',
        ]);

        $result = $initialize->run();

        $this->assertTrue($result['success']);
        $this->assertDirectoryExists($this->workDir . '/views');
        $this->assertDirectoryExists($this->workDir . '/views/shared');
        $this->assertDirectoryExists($this->workDir . '/bootstrap');
        $this->assertDirectoryExists($this->workDir . '/public');
        $this->assertDirectoryExists($this->workDir . '/config');
        $this->assertDirectoryExists($this->workDir . '/src/controllers');
        $this->assertDirectoryExists($this->workDir . '/src/models');
        $this->assertFileExists($this->workDir . '/index.php');
        $this->assertFileExists($this->workDir . '/public/index.php');
        $this->assertFileExists($this->workDir . '/config/app.conf.php');
        $this->assertFileExists($this->workDir . '/src/controllers/HomeController.php');

        $homeControllerContent = file_get_contents($this->workDir . '/src/controllers/HomeController.php');
        $this->assertStringContainsString('namespace mycustomapp\controllers;', $homeControllerContent);

        $configContent = file_get_contents($this->workDir . '/config/app.conf.php');
        $this->assertStringContainsString("'name' => \"My Custom App\"", $configContent);
        $this->assertStringContainsString("'namespace' => \"mycustomapp\"", $configContent);

        $publicIndexContent = file_get_contents($this->workDir . '/public/index.php');
        $this->assertStringContainsString("\$namespace = 'mycustomapp';", $publicIndexContent);
    }

    public function testRunUsesDefaultNamespaceAndNameWhenOptionsNotProvided(): void
    {
        $templates = Templates::getDefaultInstance();
        $ioStub = $this->createStub(Io::class);

        $initialize = new Initialize($templates, $ioStub);
        $result = $initialize->run();

        $this->assertTrue($result['success']);
        $homeControllerContent = file_get_contents($this->workDir . '/src/controllers/HomeController.php');
        $this->assertStringContainsString('namespace app\controllers;', $homeControllerContent);

        $configContent = file_get_contents($this->workDir . '/config/app.conf.php');
        $this->assertStringContainsString('An ntentan application', $configContent);
        $this->assertStringContainsString("'namespace' => \"app\"", $configContent);
    }
}
