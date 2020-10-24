<?php

declare(strict_types=1);

namespace CTags\Tests\Unit;

use CTags\Parser;
use CTags\Tag;
use CTags\Writer;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    private const FIXTURES = ['tests/fixtures/source/foo'];

    public function testParse(): void
    {
        $writer  = $this->createMock(Writer::class);
        $subject = new Parser($writer);

        $writer
            ->expects($this->exactly(1))
            ->method('write')
            ->withConsecutive(
                [new Tag('MyClass', 'tests/fixtures/source/foo/MyClass.php', '/^class MyClass$/', [])],
            );

        $subject->parse(...self::FIXTURES);
    }
}
