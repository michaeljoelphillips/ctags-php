<?php

declare(strict_types=1);

namespace CTags\Tests;

use CTags\Tag;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class TagTest extends TestCase
{
    private const TAG_WITH_EXTENSION_FIELDS    = 'name	file	/^address$/;"	kind:c	namespace:Foo\Bar';
    private const TAG_WITHOUT_EXTENSION_FIELDS = 'name	file	/^address$/';

    /**
     * @dataProvider invalidTags
     */
    public function testFromLineWithInvalidTag(string $invalidTag): void
    {
        $this->expectException(InvalidArgumentException::class);

        Tag::fromLine($invalidTag);
    }

    public function testFromLineWithTagNotContainingExtensionFields(): void
    {
        $subject = Tag::fromLine(self::TAG_WITHOUT_EXTENSION_FIELDS);

        self::assertEquals($subject->name, 'name');
        self::assertEquals($subject->file, 'file');
        self::assertEquals($subject->address, '/^address$/');
        self::assertEmpty($subject->fields);
    }

    public function testFromLineWithTagContainingExtensionFields(): void
    {
        $subject = Tag::fromLine(self::TAG_WITH_EXTENSION_FIELDS, true);

        self::assertEquals($subject->name, 'name');
        self::assertEquals($subject->file, 'file');
        self::assertEquals($subject->address, '/^address$/;"');
        self::assertNotEmpty($subject->fields);
        self::assertEquals('c', $subject->fields['kind']);
        self::assertEquals('Foo\Bar', $subject->fields['namespace']);
    }

    public function testFromLineWithTagContainingExtensionFieldsButExtensionFieldsHasBeenOverridden(): void
    {
        $subject = Tag::fromLine(self::TAG_WITH_EXTENSION_FIELDS, false);

        self::assertEquals($subject->name, 'name');
        self::assertEquals($subject->file, 'file');
        self::assertEquals($subject->address, '/^address$/;"');
        self::assertEmpty($subject->fields);
    }

    public function testToString(): void
    {
        self::assertEquals(self::TAG_WITHOUT_EXTENSION_FIELDS, (string) Tag::fromLine(self::TAG_WITHOUT_EXTENSION_FIELDS));
        self::assertEquals(self::TAG_WITH_EXTENSION_FIELDS, (string) Tag::fromLine(self::TAG_WITH_EXTENSION_FIELDS, true));
    }

    /**
     * @return array<int, array<int, string>>
     */
    public function invalidTags(): array
    {
        return [
            [''],
            ['name'],
            ['name	file'],
        ];
    }
}
