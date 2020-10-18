<?php

declare(strict_types=1);

namespace CTags\Tests;

use CTags\Reader;
use CTags\Tag;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function iterator_to_array;

class ReaderTest extends TestCase
{
    private const FIXTURE = __DIR__ . '/../fixtures/tags';

    public function testListAll(): void
    {
        $subject = Reader::fromFile(self::FIXTURE);
        $tags    = $subject->listAll();
        $tags    = iterator_to_array($tags);

        self::assertContainsOnlyInstancesOf(Tag::class, $tags);
        self::assertEquals('A', $tags[0]->name);
        self::assertEquals('vendor/phpbench/phpbench/tests/Unit/Benchmark/Remote/reflector/ClassWithClassKeywords.php', $tags[0]->file);
        self::assertEquals('/^class A$/', $tags[0]->address);
        self::assertCount(2, $tags[0]->fields);
        self::assertEquals(['kind' => 'c', 'namespace' => 'Test'], $tags[0]->fields);
    }

    public function testFilterWithPredicateReturnsOnlyMatchingTags(): void
    {
        $subject = Reader::fromFile(self::FIXTURE);
        $tags    = $subject->filter(static fn (Tag $tag) => $tag->fields['kind'] === 'i');

        foreach ($tags as $tag) {
            if ($tag->fields['kind'] === 'i') {
                continue;
            }

            $this->fail('Expected list of tags containing only interfaces');
        }

        self::addToAssertionCount(1);
    }

    public function testReaderThrowsExceptionWhenTagsFileDoesNotExist(): void
    {
        $this->expectException(RuntimeException::class);

        Reader::fromFile('/tmp/non-existent-tags-file');
    }
}
