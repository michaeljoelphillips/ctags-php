<?php

declare(strict_types=1);

namespace CTags\Tests\Benchmark;

use CTags\Reader;
use PhpBench\Benchmark\Metadata\Annotations\Assert;

use function iterator_to_array;

class ReaderBench
{
    private const FIXTURE = __DIR__ . '/../fixtures/tags';

    /**
     * @Revs(10)
     * @Iterations(10)
     * @Assert(110000)
     */
    public function benchListAllWithExtensionFields(): void
    {
        $subject = Reader::fromFile(self::FIXTURE, true);

        iterator_to_array($subject->listAll());
    }

    /**
     * @Revs(10)
     * @Iterations(10)
     * @Assert(65000)
     */
    public function benchListAllWithoutExtensionFields(): void
    {
        $subject = Reader::fromFile(self::FIXTURE, false);

        iterator_to_array($subject->listAll());
    }

    /**
     * @Revs(10)
     * @Iterations(10)
     * @Assert(15000)
     */
    public function benchMatchWithExtensionFields(): void
    {
        $subject = Reader::fromFile(self::FIXTURE, true);

        iterator_to_array($subject->match('AbstractString'));
    }

    /**
     * @Revs(10)
     * @Iterations(10)
     * @Assert(15000)
     */
    public function benchMatchWithoutExtensionFields(): void
    {
        $subject = Reader::fromFile(self::FIXTURE, false);

        iterator_to_array($subject->match('AbstractString'));
    }

    /**
     * @Revs(10)
     * @Iterations(10)
     * @Assert(15000)
     */
    public function benchMatchPartialWithExtensionFields(): void
    {
        $subject = Reader::fromFile(self::FIXTURE, true);

        iterator_to_array($subject->match('Abstract'));
    }

    /**
     * @Revs(10)
     * @Iterations(10)
     * @Assert(15000)
     */
    public function benchMatchPartialWithoutExtensionFields(): void
    {
        $subject = Reader::fromFile(self::FIXTURE, false);

        iterator_to_array($subject->match('Abstract'));
    }
}
