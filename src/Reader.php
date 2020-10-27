<?php

declare(strict_types=1);

namespace CTags;

use Generator;
use RuntimeException;

use function fgets;
use function file_exists;
use function fopen;
use function fseek;
use function ftell;
use function preg_match;
use function sprintf;
use function trim;

class Reader
{
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

    private function resetFilePointer(): void
    {
        static $lastPosition = null;

        if ($lastPosition !== null) {
            fseek($this->tagFile, $lastPosition);

            return;
        }

        fseek($this->tagFile, 0);

        while ($tagLine = fgets($this->tagFile)) {
            if ($tagLine[0] !== '!') {
                fseek($this->tagFile, $lastPosition);

                break;
            }

            $lastPosition = ftell($this->tagFile);
        }
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
        $this->resetFilePointer();

        while ($tagLine = fgets($this->tagFile)) {
            $tagLine = Tag::fromLine(trim($tagLine), $this->includeExtensionFields);

            if ($predicate($tagLine) === false) {
                continue;
            }

            yield $tagLine;
        }
    }

    public function partialMatch(string $name): Generator
    {
        $this->resetFilePointer();

        while ($tagLine = fgets($this->tagFile)) {
            if (preg_match('/^' . $name . '\w*\t/', $tagLine) !== 1) {
                continue;
            }

            yield Tag::fromLine(trim($tagLine), $this->includeExtensionFields);
        }
    }

    public function match(string $name): Generator
    {
        $this->resetFilePointer();

        while ($tagLine = fgets($this->tagFile)) {
            if (preg_match('/^' . $name . '\t/', $tagLine) !== 1) {
                continue;
            }

            yield Tag::fromLine(trim($tagLine), $this->includeExtensionFields);
        }
    }
}
