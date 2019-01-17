<?php

declare(strict_types = 1);

namespace Stoffel\ComposerBook;

use Symfony\Component\Finder\SplFileInfo;

class SourceFile
{
    private $fileInfo;

    public function __construct(SplFileInfo $fileInfo)
    {
        $this->fileInfo = $fileInfo;
    }

    public function getPathname(): string
    {
        return $this->fileInfo->getRelativePathname();
    }

    public function getSource(): string
    {
        return $this->fileInfo->getContents();
    }

    public function isPHP(): bool
    {
        return 'php' === $this->fileInfo->getExtension()
            || '.php_cs' === $this->fileInfo->getRelativePathname()
            || 'bin/compile' === $this->fileInfo->getRelativePathname()
            || 'bin/composer' === $this->fileInfo->getRelativePathname();
    }

    public function isMarkdown(): bool
    {
        return 'md' === $this->fileInfo->getExtension();
    }

    public function isXML(): bool
    {
        return 'xml' === $this->fileInfo->getExtension()
            || 'phpunit.xml.dist' === $this->fileInfo->getFilename();
    }

    public function isJSON(): bool
    {
        return 'json' === $this->fileInfo->getExtension()
            || 'composer.lock' === $this->fileInfo->getFilename();
    }

    public function isAuthorList(): bool
    {
        return 'authors.csv' === $this->fileInfo->getFilename();
    }
}
