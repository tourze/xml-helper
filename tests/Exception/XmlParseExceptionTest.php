<?php

declare(strict_types=1);

namespace Tourze\XML\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;
use Tourze\XML\Exception\XmlParseException;

/**
 * @internal
 */
#[CoversClass(XmlParseException::class)]
final class XmlParseExceptionTest extends AbstractExceptionTestCase
{
    public function testExceptionMessage(): void
    {
        $this->expectException(XmlParseException::class);
        $this->expectExceptionMessage('XML parsing failed');

        throw new XmlParseException('XML parsing failed');
    }

    public function testExceptionCode(): void
    {
        $this->expectException(XmlParseException::class);
        $this->expectExceptionCode(100);

        throw new XmlParseException('XML parsing failed', 100);
    }

    public function testExceptionInheritance(): void
    {
        $exception = new XmlParseException('test message');

        $this->assertInstanceOf(\RuntimeException::class, $exception);
        $this->assertInstanceOf(XmlParseException::class, $exception);
    }
}
