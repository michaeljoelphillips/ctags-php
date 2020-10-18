<?php

declare(strict_types=1);

namespace CTags;

use function array_column;
use function array_map;
use function array_slice;
use function count;
use function explode;
use function substr;

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
    public static function fromLine(string $tagLine): self
    {
        $tagLine                 = explode("\t", $tagLine);
        [$name, $file, $address] = $tagLine;

        $fields = self::parseExtensionTagFields($tagLine);

        if (empty($fields) === false) {
            $address = substr($address, 0, -2);
        }

        return new self($name, $file, $address, $fields);
    }

    /**
     * @param array<int, string> $tagLine
     *
     * @return array<string, string>
     */
    private static function parseExtensionTagFields(array $tagLine): array
    {
        $fields = array_map(
            static function (string $field): array {
                $field = explode(':', $field);

                if (count($field) === 1) {
                    $field = ['kind', $field[0]];
                }

                return $field;
            },
            array_slice($tagLine, 3)
        );

        return array_column($fields, 1, 0);
    }
}
