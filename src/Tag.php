<?php

declare(strict_types=1);

namespace CTags;

use function array_slice;
use function count;
use function explode;
use function sprintf;

class Tag
{
    public string $name;
    public string $file;
    public string $address;

    /** @var array<string, string> */
    public array $fields;

    /**
     * @param array<string, string> $fields
     */
    public function __construct(
        string $name,
        string $file,
        string $address,
        ?array $fields
    ) {
        $this->name    = $name;
        $this->file    = $file;
        $this->address = $address;
        $this->fields  = $fields;
    }

    /**
     * @param array<int, string> $tag
     */
    public static function fromLine(string $tagLine, bool $includeExtensionFields = false): self
    {
        $tagLine                 = explode("\t", $tagLine);
        [$name, $file, $address] = $tagLine;
        $fields                  = $includeExtensionFields ? self::parseExtensionFields($tagLine) : [];

        return new self($name, $file, $address, $fields);
    }

    /**
     * @param array<int, string> $tagLine
     *
     * @return array<string, string>
     */
    private static function parseExtensionFields(array $tagLine): array
    {
        $extensionFields = [];

        foreach (array_slice($tagLine, 3) as $field) {
            $field = explode(':', $field);

            if (count($field) === 1) {
                $field = ['kind', $field[0]];
            }

            $extensionFields[$field[0]] = $field[1];
        }

        return $extensionFields;
    }

    public function __toString(): string
    {
        return sprintf("%s\t%s\t%s\n", $this->name, $this->file, $this->address);
    }
}
