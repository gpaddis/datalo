<?php namespace Dataloader;

use League\Csv\Reader;
use League\Csv\Writer;
use Dataloader\Parsers\Parser;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

abstract class Command extends SymfonyCommand
{
    use CommandHelpersTrait;

    /**
     * The Parser instance.
     *
     * @var Dataloader\Parsers\Parser
     */
    protected $parser;

    /**
     * Command constructor.
     *
     * @param Dataloader\Parsers\Parser $parser
     */
    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
        parent::__construct();
    }

    /**
     * Execute the command.
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $source = $input->getArgument('source');
        $destination = $input->getArgument('destination');
        $delimiter = $input->getOption('delimiter');
        $status = $input->getOption('status');

        // Check if the source file exists or is empty.
        if (!file_exists($source) || $this->isEmpty($source)) {
            throw new \RuntimeException("You are trying to open an invalid file. Try with another one.");
        }

        // Unless --force is set, check if destination file already exists.
        if (! $input->getOption('force')) {
            $this->verifyDestinationDoesntExist($destination);
        }

        // Autodetect the delimiter for the file if none is specified.
        $csv = Reader::createFromPath($source);

        $delimiter = $delimiter ?? $this->autodetectDelimiter($csv);
        $csv->setDelimiter($delimiter);

        // Check if the parser finds columns containing identifiers.
        $first25Rows = $csv->setOffset(1)->setLimit(25)->fetchAll();
        if (! $indexes = $this->parser->findAllIndexes($first25Rows)) {
            throw new \RuntimeException("No identifiers found in the source file.");
        }

        $output->writeln(sprintf('<info>Found %s column(s) containing identifiers.</info>', count($indexes)));
        $output->writeln(sprintf('Processing file...', count($indexes)));

        // Create a new progress bar.
        $progress = new ProgressBar($output, $this->countLines($source));
        $progress->setRedrawFrequency(100);
        $progress->start();

        // Process all identifiers.
        $identifiersCount = 0;
        $rowsCount = 0;
        foreach ($csv as $row) {
            $identifiers = $this->parser->collectIdentifiers($row, $indexes);
            foreach ($identifiers as $identifier) {
                $content[] = [$identifier, $status];
                $identifiersCount++;
            }
            $rowsCount++;
            $progress->advance();
        }

        // Save the content to the file
        $writer = Writer::createFromPath($destination, 'w+')
        ->setDelimiter("\t")
        ->insertAll($content);

        $progress->finish();
        $output->writeln("");
        $output->writeln("{$rowsCount} rows processed succesfully, {$identifiersCount} unique identifiers found.");

        // Confirm the result of the operation.
        $output->writeln("You can upload this file to the dataloader: <info>{$destination}.</info>");
    }
}
