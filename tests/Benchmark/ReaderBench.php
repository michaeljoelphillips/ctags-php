<?php

declare(strict_types=1);

namespace CTags\Tests\Benchmark;

use PhpBench\Benchmark\Metadata\Annotations\Assert;
use CTags\Reader;

use function iterator_to_array;

class ReaderBench
{
    private const FIXTURE = __DIR__ . '/../fixtures/tags';

    /**
     * @Revs(100)
     * @Assert(150000)
     */
    public function benchListAll(): void
    {
        $subject = Reader::fromFile(self::FIXTURE);

        iterator_to_array($subject->listAll());
    }
}
