<?php

declare(strict_types=1);

namespace CTags;

use AppendIterator;
use FileSystemIterator;
use FilterIterator;
use Iterator;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\NodeAbstract;
use PhpParser\NodeFinder;
use PhpParser\Parser as AstParser;
use PhpParser\ParserFactory;
use SplFileInfo;

use function array_map;
use function file_get_contents;
use function preg_match;

class Parser
{
    private Writer $writer;

    private AstParser $astParser;

    public function __construct(Writer $writer, ?AstParser $astParser = null)
    {
        $this->writer     = $writer;
        $this->nodeFinder = new NodeFinder();

        $this->setAstParser($astParser);
    }

    private function setAstParser(?AstParser $astParser): void
    {
        $this->astParser = $astParser ?: (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
    }

    public function parse(string ...$paths): void
    {
        $source = $this->prepareSource(...$paths);

        foreach ($source as $file) {
            $contents = file_get_contents((string) $file);
            $nodes    = $this->astParser->parse($contents);

            $tagNodes = $this->nodeFinder->find($nodes, static function (NodeAbstract $node): bool {
                return $node instanceof Class_ && $node->isAnonymous() === false;
            });

            $this->writeTags($tagNodes, $file->getPathName());
        }
    }

    /**
     * @param array<int, ClassLike> $tagNodes
     */
    private function writeTags(array $tagNodes, string $filePath): void
    {
        $tags = array_map(
            static function (ClassLike $node) use ($filePath): Tag {
                return new Tag((string) $node->name, $filePath, '/^class MyClass$/', []);
            },
            $tagNodes
        );

        foreach ($tags as $tag) {
            $this->writer->write($tag);
        }
    }

    /**
     * @return Iterator<string, SplFileInfo>
     */
    private function prepareSource(string ...$paths): Iterator
    {
        $directories = new AppendIterator();

        foreach ($paths as $path) {
            $directories->append(new FileSystemIterator($path));
        }

        return new class ($directories) extends FilterIterator {
            public function accept(): bool
            {
                $file = parent::current();

                return preg_match('/^.*\.php$/', (string) $file) === 1;
            }
        };
    }
}
