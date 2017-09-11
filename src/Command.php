<?php

namespace Dataloader;

use Dataloader\Parsers\Parser;
use League\Csv\Reader;
use League\Csv\Writer;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
     * The delimiters used for autodetection.
     *
     * @var array
     */
    protected $delimiters = [',', "\t", ';'];

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
     * @param Symfony\Component\Console\Input\InputInterface   $input
     * @param Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $source = $input->getArgument('source');
        $destination = $input->getArgument('destination');
        $delimiter = $input->getOption('delimiter');
        $status = $input->getOption('status');

        $this->validateFile($source);

        // Unless --force is set, check if destination file already exists.
        if (!$input->getOption('force')) {
            $this->verifyDestinationDoesntExist($destination);
        }

        $csv = Reader::createFromPath($source);

        // Autodetect the delimiter for the file if none is specified.
        $delimiter = $delimiter ?? $this->autodetectDelimiter($source);
        $csv->setDelimiter($delimiter);

        // Check if the parser finds columns containing identifiers.
        $indexes = $this->findIdentifierColumns($csv);

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
        $output->writeln('');
        $output->writeln("{$rowsCount} rows processed succesfully, {$identifiersCount} unique identifiers found.");

        // Confirm the result of the operation.
        $output->writeln("You can upload this file to the dataloader: <info>{$destination}.</info>");
    }
}
