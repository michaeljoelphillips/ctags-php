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
        $subject = Reader::fromFile(self::FIXTURE, true);
        $tags    = $subject->listAll();
        $tags    = iterator_to_array($tags);

        self::assertCount(14768, $tags);
        self::assertContainsOnlyInstancesOf(Tag::class, $tags);
        self::assertEquals('A', $tags[0]->name);
        self::assertEquals('vendor/phpbench/phpbench/tests/Unit/Benchmark/Remote/reflector/ClassWithClassKeywords.php', $tags[0]->file);
        self::assertEquals('/^class A$/;"', $tags[0]->address);
        self::assertCount(2, $tags[0]->fields);
        self::assertEquals(['kind' => 'c', 'namespace' => 'Test'], $tags[0]->fields);
    }

    public function testFilter(): void
    {
        $subject = Reader::fromFile(self::FIXTURE, true);
        $tags    = $subject->filter(static fn (Tag $tag) => $tag->fields['kind'] === 'i');

        foreach ($tags as $tag) {
            if ($tag->fields['kind'] === 'i') {
                continue;
            }

            $this->fail('Expected list of tags containing only interfaces');
        }

        self::addToAssertionCount(1);
    }

    public function testMatch(): void
    {
        $subject = Reader::fromFile(self::FIXTURE, true);
        $tags    = $subject->match('AbstractString');
        $tags    = iterator_to_array($tags);

        self::assertCount(1, $tags);
        self::assertContainsOnlyInstancesOf(Tag::class, $tags);
    }

    public function testPartialMatch(): void
    {
        $subject = Reader::fromFile(self::FIXTURE, true);
        $tags    = $subject->partialMatch('Abstract');
        $tags    = iterator_to_array($tags);

        self::assertCount(33, $tags);
        self::assertContainsOnlyInstancesOf(Tag::class, $tags);
    }

    public function testMultipleCallsToMatchReturnTheSameResult(): void
    {
        $subject      = Reader::fromFile(self::FIXTURE, true);
        $firstResult  = iterator_to_array($subject->match('AbstractString'));
        $secondResult = iterator_to_array($subject->match('AbstractString'));

        self::assertEquals($firstResult, $secondResult);
    }

    public function testReaderThrowsExceptionWhenTagsFileDoesNotExist(): void
    {
        $this->expectException(RuntimeException::class);

        Reader::fromFile('/tmp/non-existent-tags-file');
    }
}
