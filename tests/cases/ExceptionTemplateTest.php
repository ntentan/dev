<?php

namespace ntentan\dev\tests\cases;

use PHPUnit\Framework\TestCase;
use RuntimeException;

class ExceptionTemplateTest extends TestCase
{
    public function testExceptionTemplateRendersExceptionDetails(): void
    {
        $exception = new RuntimeException('Database connection failed');

        ob_start();
        require __DIR__ . '/../../src/exception.php';
        $html = ob_get_clean();

        $this->assertStringContainsString('<title>Ntentan Exception - Database connection failed</title>', $html);
        $this->assertStringContainsString('<h1>RuntimeException</h1>', $html);
        $this->assertStringContainsString('<p>Database connection failed</p>', $html);
        $this->assertStringContainsString((string) $exception->getLine(), $html);
        $this->assertStringContainsString($exception->getFile(), $html);
        $this->assertStringContainsString('<h2>Stack Trace</h2>', $html);
    }
}

