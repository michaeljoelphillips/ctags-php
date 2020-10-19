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

    private bool $includeExtensionFields;

    /**
     * @param resource $tagFile
     */
    private function __construct($tagFile, bool $includeExtensionFields)
    {
        $this->tagFile                = $tagFile;
        $this->includeExtensionFields = $includeExtensionFields;
    }

    public static function fromFile(string $file, bool $includeExtensionFields = false): self
    {
        if (file_exists($file) === false) {
            throw new RuntimeException(sprintf('No such file: %s', $file));
        }

        return new self(fopen($file, 'r'), $includeExtensionFields);
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

            $tagLine = Tag::fromLine(trim($tagLine), $this->includeExtensionFields);

            if ($predicate($tagLine) === false) {
                continue;
            }

            yield $tagLine;
        }
    }

    public function match(string $name): Generator
    {
        return $this->filter(static fn (Tag $tag) => $tag->name === $name);
    }
}
