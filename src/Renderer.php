<?php

declare(strict_types = 1);

namespace Stoffel\ComposerBook;

use HTMLPurifier;
use json2html\PrettyJSON;
use mikehaertl\wkhtmlto\Pdf;
use Parsedown;

class Renderer
{
    private $parsedown;
    private $htmlPurifier;
    private $prettyJSON;

    public function __construct(Parsedown $parsedown, HTMLPurifier $htmlPurifier, PrettyJSON $prettyJSON)
    {
        $this->parsedown = $parsedown;
        $this->htmlPurifier = $htmlPurifier;
        $this->prettyJSON = $prettyJSON;
    }

    public function render(Book $book, string $output): void
    {
        $pdf = new Pdf([
            'commandOptions' => ['useExec' => true],
            'title' => $book->getTitle(),
            'user-style-sheet' => $this->getResource('style.css'),
            // Margin
            'margin-top' => 15,
            'margin-right' => 15,
            'margin-bottom' => 12,
            'margin-left' => 15,
            // Header
            'header-left' => sprintf('%s - [chapter]', $book->getTitle()),
            'header-font-name' => 'sans-serif',
            'header-font-size' => 9,
            'header-spacing' => 6,
            'header-line',
            // Footer
            'footer-left' => 'getcomposer.org',
            'footer-right' => '[page]',
            'footer-font-name' => 'sans-serif',
            'footer-font-size' => 9,
            'footer-spacing' => 4,
            'footer-line',
        ]);
        $pdf->addCover($this->getResource('cover.html'));
        $pdf->addCover($this->getResource('blank.html'));
        $pdf->addCover($this->getResource('subtitle.html'));
        $pdf->addCover($this->getResource('blank.html'));
        $pdf->addCover($this->getResource('prologue.html'));
        $pdf->addCover($this->getResource('blank.html'));
        $pdf->addPage($this->getResource('toc.html') , [
            'replace' => ['chapter' => 'Table of Contents'],
        ]);

        foreach ($book() as $chapter) {
            $pdf->addPage($this->renderChapter($chapter), [
                'replace' => ['chapter' => $chapter->getName()],
            ]);
            foreach ($chapter() as $file) {
                if ($file->isAuthorList()) {
                    $header = sprintf('%s - [chapter]', $book->getTitle());
                } else {
                    $header = sprintf('%s - [chapter] - [file]', $book->getTitle());
                }
                $pdf->addPage(
                    $this->htmlPurifier->purify($this->renderFile($file)),
                    [
                        'header-left' => $header,
                        'encoding' => 'UTF-8',
                        'replace' => [
                            'chapter' => $chapter->getName(),
                            'file' => $file->getPathname(),
                        ],
                    ],
                    Pdf::TYPE_HTML
                );
            }
        }

        $pdf->addCover($this->getResource('blank.html'));
        $pdf->addCover($this->getResource('end.html'));

        $pdf->addToc();

        if (!$pdf->saveAs($output)) {
            throw new \RuntimeException($pdf->getError());
        }
    }

    private function renderChapter(Chapter $chapter): string
    {
        $template = file_get_contents($this->getResource('chapter.html'));

        return str_replace('[chapter]', $chapter->getName(), $template);
    }

    private function renderFile(SourceFile $file): string
    {
        $source = $file->getSource();

        switch (true) {
            case $file->isMarkdown():
                return $this->parsedown->text($source);
            case $file->isPHP():
                return highlight_string($source, true);
            case $file->isAuthorList():
                return (new AuthorsRenderer())->render($source);
            case $file->isJSON():
                $this->prettyJSON->setJsonText($source);

                return $this->prettyJSON->getHtml();
            case $file->isXML():
                $source = str_replace(' ', '&nbsp;', htmlspecialchars($source));
        }

        return sprintf('<code>%s</code>', nl2br($source));
    }

    private function getResource(string $name): string
    {
        return __DIR__ . '/../Resources/' . $name;
    }
}
