<?php

namespace ntentan\dev\tests\cases;

use ntentan\dev\assets\builders\ConcatBuilder;
use ntentan\dev\assets\builders\CssBuilder;
use ntentan\dev\assets\builders\JsBuilder;
use ntentan\dev\tests\lib\TempDirectoryTestCase;

class ConcatBuilderTest extends TempDirectoryTestCase
{
    public function testConcatBuilderDescriptionAndBuild(): void
    {
        $file1 = $this->assetsDir . '/part1.txt';
        $file2 = $this->assetsDir . '/part2.txt';
        file_put_contents($file1, 'First part');
        file_put_contents($file2, 'Second part');

        $builder = new ConcatBuilder();
        $this->assertSame('concatenator', $builder->getDescription());

        $builder->setInputs([$file1, $file2]);
        $builder->setOutputFile('combined.txt');
        $builder->build();

        $outputFile = $builder->getOutputFile();
        $this->assertFileExists($outputFile);
        $this->assertSame("\nFirst part\nSecond part", file_get_contents($outputFile));
    }

    public function testCssBuilderInheritsFromConcatBuilder(): void
    {
        $css1 = $this->assetsDir . '/reset.css';
        $css2 = $this->assetsDir . '/theme.css';
        file_put_contents($css1, '* { box-sizing: border-box; }');
        file_put_contents($css2, 'body { color: #111; }');

        $builder = new CssBuilder();
        $this->assertInstanceOf(ConcatBuilder::class, $builder);
        $this->assertSame('concatenator', $builder->getDescription());

        $builder->setInputs([$css1, $css2]);
        $builder->setOutputFile('css/all.css');
        $builder->build();

        $this->assertSame(
            "\n* { box-sizing: border-box; }\nbody { color: #111; }",
            file_get_contents($builder->getOutputFile())
        );
    }

    public function testJsBuilderInheritsFromConcatBuilder(): void
    {
        $js1 = $this->assetsDir . '/util.js';
        $js2 = $this->assetsDir . '/init.js';
        file_put_contents($js1, 'function util() {}');
        file_put_contents($js2, 'util();');

        $builder = new JsBuilder();
        $this->assertInstanceOf(ConcatBuilder::class, $builder);
        $this->assertSame('concatenator', $builder->getDescription());

        $builder->setInputs([$js1, $js2]);
        $builder->setOutputFile('js/bundle.js');
        $builder->build();

        $this->assertSame(
            "\nfunction util() {}\nutil();",
            file_get_contents($builder->getOutputFile())
        );
    }
}

