<?php

declare(strict_types = 1);

namespace Stoffel\ComposerBook;

class AuthorsRenderer
{
    public function render(string $content): string
    {
        $top = [];
        $main = [];
        $regular = [];
        $member = [];
        $onetime = [];
        $authors = str_getcsv($content, PHP_EOL);
        foreach ($authors as $author) {
            $data = str_getcsv($author, ",");
            $commits = (int) $data[0];
            $name = $data[1];
            switch (true) {
                case $commits >= 500:
                    $top[$name] = $commits;
                    break;
                case $commits >= 50:
                    $main[$name] = $commits;
                    break;
                case $commits >= 10:
                    $regular[$name] = $commits;
                    break;
                case $commits > 1:
                    $member[$name] = $commits;
                    break;
                default:
                    $onetime[$name] = $commits;
                    break;
            }
        }

        arsort($top);
        arsort($main);
        arsort($regular);
        arsort($member);
        arsort($onetime);

        $template = file_get_contents(__DIR__.'/../Resources/authors.html');

        $search = ['[top]', '[main]', '[regular]', '[member]', '[onetime]'];
        $replace = [
            implode(' - ', array_keys($top)),
            implode(' - ', array_keys($main)),
            implode(' - ', array_keys($regular)),
            implode(' - ', array_keys($member)),
            implode(' - ', array_keys($onetime)),
        ];

        return str_replace($search, $replace, $template);
    }
}
