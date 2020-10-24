<?php

declare(strict_types=1);

namespace CTags;

use function fclose;
use function fopen;
use function fwrite;

use const DIRECTORY_SEPARATOR;

class Writer
{
    /** @var resource */
    private $file;

    public function __construct()
    {
        $this->file = fopen(__DIR__ . DIRECTORY_SEPARATOR . 'tags', 'w');
    }

    public function __destruct()
    {
        fclose($this->file);
    }

    public function write(Tag $tag): void
    {
        fwrite($this->file, (string) $tag);
    }
}
