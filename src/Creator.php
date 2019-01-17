<?php

declare(strict_types = 1);

namespace Stoffel\ComposerBook;

class Creator
{
    private $renderer;

    public function __construct(Renderer $renderer)
    {
        $this->renderer = $renderer;
    }

    public function createBook(string $input, string $output): void
    {
        $book = new Book(
            'Composer 1.8.0',
            Chapter::readme($input),
            Chapter::changelog($input),
            Chapter::docs($input),
            Chapter::source($input),
            Chapter::other($input),
            Chapter::authors()
        );

        $this->renderer->render($book, $output);
    }
}
