<?php

declare(strict_types = 1);

namespace Stoffel\ComposerBook;

use Symfony\Component\Finder\Finder;

class Chapter
{
    private $name;
    private $finder;

    public function __construct(string $name, Finder $finder)
    {
        $this->name = $name;
        $this->finder = $finder;
    }

    public static function readme(string $input): self
    {
        $finder = Finder::create()
            ->files()
            ->in($input)
            ->name('README.md')
            ->exclude('vendor');

        return new self('Readme', $finder);
    }

    public static function changelog(string $input): self
    {
        $finder = Finder::create()
            ->files()
            ->in($input)
            ->name('CHANGELOG.md')
            ->exclude('vendor');

        return new self('Changelog', $finder);
    }

    public static function docs(string $input): self
    {
        $finder = Finder::create()
            ->files()
            ->in($input)
            ->path('doc')
            ->exclude(['doc/fixtures', 'tests', 'vendor'])
            ->sortByName();

        return new self('The Documentation', $finder);
    }

    public static function source(string $input): self
    {
        $finder = Finder::create()
            ->files()
            ->in($input)
            ->path('bin')
            ->path('src')
            ->exclude(['doc', 'tests', 'vendor'])
            ->sortByName();

        return new self('The Source Code', $finder);
    }

    public static function other(string $input): self
    {
        $finder = Finder::create()
            ->files()
            ->in($input)
            ->exclude(['.idea', 'bin', 'doc', 'src', 'tests', 'vendor'])
            ->ignoreDotFiles(false)
            ->notName('README.md')
            ->notName('CHANGELOG.md')
            ->sortByName();

        return new self('Other Files', $finder);
    }

    public static function authors(): self
    {
        $finder = Finder::create()
            ->files()
            ->in(__DIR__.'/../')
            ->path('Resources')
            ->name('authors.csv');

        return new self('Authors', $finder);
    }

    /**
     * @return SourceFile[]|\Generator
     */
    public function __invoke(): \Generator
    {
        foreach ($this->finder as $fileInfo) {
            yield new SourceFile($fileInfo);
        }
    }

    public function getName(): string
    {
        return $this->name;
    }
}
