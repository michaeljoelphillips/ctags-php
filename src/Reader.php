<?php

declare(strict_types=1);

namespace CTags;

use Generator;
use RuntimeException;

use function fgets;
use function file_exists;
use function fopen;
use function sprintf;
use function strpos;
use function trim;

class Reader
{
    private const TAGFILE_METADATA_PREFIX = '!_TAG_';

    /** @var resource */
    private $tagFile;

    /**
     * @param resource $tagFile
     */
    private function __construct($tagFile)
    {
        $this->tagFile = $tagFile;
    }

    public static function fromFile(string $file): self
    {
        if (file_exists($file) === false) {
            throw new RuntimeException(sprintf('No such file: %s', $file));
        }

        return new self(fopen($file, 'r'));
    }

    public function listAll(): Generator
    {
        return $this->filter(static fn () => true);
    }

    /**
     * @param callable(Tag $tag): bool $predicate
     */
    public function filter(callable $predicate): Generator
    {
        while ($tagLine = fgets($this->tagFile)) {
            if (strpos($tagLine, self::TAGFILE_METADATA_PREFIX) !== false) {
                continue;
            }

            $tagLine = Tag::fromLine(trim($tagLine));

            if ($predicate($tagLine) === false) {
                continue;
            }

            yield $tagLine;
        }
    }
}
