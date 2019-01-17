<?php

declare(strict_types = 1);

namespace Stoffel\ComposerBook;

class Book
{
    private $title;
    private $chapters;

    public function __construct(string $name, Chapter ...$chapters)
    {
        $this->title = $name;
        $this->chapters = $chapters;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return Chapter[]|\Generator
     */
    public function __invoke(): \Generator
    {
        foreach ($this->chapters as $chapter) {
            yield $chapter;
        }
    }
}
