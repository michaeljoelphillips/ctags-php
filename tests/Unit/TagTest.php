<?php

declare(strict_types=1);

namespace CTags\Tests\Unit;

use CTags\Tag;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class TagTest extends TestCase
{
    public function testFromLineGuardsAgainstInvalidTagLines(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Tag::fromLine('InvalidTag	src/InvalidTag.php');
    }

    public function testIsExtendedWhenExtendedFieldsArePresent(): void
    {
        $subject = new Tag('TestTag', 'src/TestTag.php', '/^class TestTag$/;"', ['kind' => 'class', 'namespace' => 'App']);

        self::assertTrue($subject->isExtended());
    }

    public function testIsExtendedWhenExtendedFieldsAreNotPresent(): void
    {
        $subject = new Tag('TestTag', 'src/TestTag.php', '/^class TestTag$/;"', []);

        self::assertFalse($subject->isExtended());
    }

    /**
     * @dataProvider tagLineProvider
     */
    public function testToStringMatchesOriginalTagLine(bool $includeExtensionFields, string $tagline): void
    {
        $subject = Tag::fromLine($tagline, $includeExtensionFields);

        self::assertEquals($tagline, (string) $subject);
    }

    /**
     * @return array<int, array<int, string>>
     */
    public function tagLineProvider(): array
    {
        return [
            [false, 'Reader	src/Reader.php	/^class Reader$/'],
            [true, 'Reader	src/Reader.php	/^class Reader$/;"	kind:c	namespace:CTags'],
        ];
    }
}
