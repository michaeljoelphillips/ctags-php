<?php

declare(strict_types=1);

namespace CTags\Console;

use CTags\Parser;
use CTags\Writer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateTags extends Command
{
    protected static $defaultName = 'tags:generate';

    protected function configure(): void
    {
        $this->addArgument('files', InputArgument::IS_ARRAY);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $parser = new Parser(new Writer());

        $parser->parse(...$input->getArgument('files'));

        return 0;
    }
}
