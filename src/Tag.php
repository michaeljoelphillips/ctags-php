<?php

declare(strict_types=1);

namespace CTags;

use InvalidArgumentException;

use function array_slice;
use function count;
use function explode;
use function implode;
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
        $tagLineParts = explode("\t", $tagLine);

        if (count($tagLineParts) < 3) {
            throw new InvalidArgumentException(sprintf('Invalid tag given: %s', $tagLine));
        }

        [$name, $file, $address] = $tagLineParts;
        $fields                  = $includeExtensionFields === true ? self::parseExtensionFields($tagLineParts) : [];

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

    public function isExtended(): bool
    {
        return empty($this->fields) === false;
    }

    public function __toString(): string
    {
        if ($this->isExtended() === false) {
            return sprintf("%s\t%s\t%s", $this->name, $this->file, $this->address);
        }

        $fields = [];
        foreach ($this->fields as $key => $value) {
            $fields[] = sprintf('%s:%s', $key, $value);
        }

        return sprintf("%s\t%s\t%s\t%s", $this->name, $this->file, $this->address, implode("\t", $fields));
    }
}
