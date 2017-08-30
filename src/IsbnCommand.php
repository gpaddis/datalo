<?php namespace Dataloader;

use Dataloader\Parsers\IsbnParser;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class IsbnCommand extends Command
{
    /**
     * IsbnCommand constructor.
     */
    public function __construct()
    {
        parent::__construct(new IsbnParser);
    }

    /**
     * Configure the command options.
     *
     * @return void
     */
    public function configure()
    {
        $this->setName('isbn')
        ->setDescription('Generate a list of ISBNs')
        ->addArgument('source', InputArgument::REQUIRED, 'The delimiter-separated source file you want to process')
        ->addArgument('destination', InputArgument::REQUIRED, 'The name of the tab separated file for the SFX Dataloader')
        ->addOption('delimiter', null, InputOption::VALUE_REQUIRED, 'Set the delimiter for the source file (comma/tab/semicolon)', 'comma')
        ->addOption('status', null, InputOption::VALUE_REQUIRED, 'Set the desired status for the portfolios (e.g. ACTIVE, INACTIVE)', 'ACTIVE')
        ->addOption('force', null, InputOption::VALUE_NONE, 'Overwrite the destination file if it already exists');
    }
}
