<?php

declare(strict_types = 1);

namespace Stoffel\ComposerBook;

use HTMLPurifier;
use json2html\PrettyJSON;
use Parsedown;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateCommand extends Command
{
    private $creator;

    public function __construct()
    {
        parent::__construct();

        $purifierConfig = \HTMLPurifier_Config::create(['Cache.DefinitionImpl' => null]);

        $this->creator = new Creator(
            new Renderer(new Parsedown(), new HTMLPurifier($purifierConfig), new PrettyJSON())
        );
    }

    protected function configure(): void
    {
        $this
            ->setName('create')
            ->setDescription('Creates Composer Book as PDF by Composer\'s source code')
            ->addOption('input', 'i', InputOption::VALUE_REQUIRED, 'Path to Composer Source', __DIR__.'/../../composer')
            ->addOption('output', 'o', InputOption::VALUE_REQUIRED, 'Output PDF', 'composer.pdf');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Creating Composer as PDF');

        $inputPath = $input->getOption('input');
        $outputPath = $input->getOption('output');

        $this->creator->createBook($inputPath, $outputPath);

        $io->success(sprintf('Successfully generated %s.', $outputPath));

        return 0;
    }
}
