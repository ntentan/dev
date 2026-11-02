<?php

namespace ntentan\dev\tests\cases;

use ntentan\dev\assets\builders\CopyBuilder;
use ntentan\dev\tests\lib\TempDirectoryTestCase;

class CopyBuilderTest extends TempDirectoryTestCase
{
    public function testGetDescription(): void
    {
        $builder = new CopyBuilder();
        $builder->setOutputFile('dummy.txt');
        $this->assertSame('copier', $builder->getDescription());
    }

    public function testBuildWithTrailingSlashCreatesDestinationDirectory(): void
    {
        $file1 = $this->assetsDir . '/icon.png';
        $file2 = $this->assetsDir . '/banner.jpg';
        file_put_contents($file1, 'PNG_DATA');
        file_put_contents($file2, 'JPG_DATA');

        $builder = new CopyBuilder();
        $builder->setInputs([$file1, $file2]);
        $builder->setOutputFile('static/img/');
        $builder->build();

        $this->assertFileExists($this->publicDir . '/static/img/icon.png');
        $this->assertFileExists($this->publicDir . '/static/img/banner.jpg');
        $this->assertSame('PNG_DATA', file_get_contents($this->publicDir . '/static/img/icon.png'));
        $this->assertSame('JPG_DATA', file_get_contents($this->publicDir . '/static/img/banner.jpg'));
    }

    public function testBuildWithoutTrailingSlashCopiesIntoParentDirectory(): void
    {
        $file = $this->assetsDir . '/robots.txt';
        file_put_contents($file, 'User-agent: *');

        $builder = new CopyBuilder();
        $builder->setInputs([$file]);
        $builder->setOutputFile('sub/robots.txt');
        $builder->build();

        $this->assertFileExists($this->publicDir . '/sub/robots.txt');
        $this->assertSame('User-agent: *', file_get_contents($this->publicDir . '/sub/robots.txt'));
    }
}

