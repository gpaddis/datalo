<?php namespace Dataloader;

use League\Csv\Reader;
use Dataloader\Parsers\IsbnParser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConvertIsbnCommand extends Command
{
	use ConverterFunctionsTrait;
	/**
	 * An array of the allowed delimiters.
	 *
	 * @var array
	 */
	protected $delimiters = [
	'comma' => ',',
	'tab' => '	',
	'semicolon' => ';'
	];

	/**
	 * Configure the command options.
	 *
	 * @return void
	 */
	public function configure()
	{
		$this->setName('convert:isbn')
		->setDescription('Generate a list of ISBNs')
		->addArgument('source', InputArgument::REQUIRED, 'The delimiter-separated source file you want to process')
		->addArgument('destination', InputArgument::REQUIRED, 'The name of the tab separated file for the SFX Dataloader')
		->addOption('delimiter', null, InputOption::VALUE_REQUIRED, 'Set the delimiter for the source file (comma/tab/semicolon)', 'comma')
		->addOption('status', null, InputOption::VALUE_REQUIRED, 'Set the desired status for the portfolios (e.g. ACTIVE, INACTIVE)', 'ACTIVE')
		->addOption('force', null, InputOption::VALUE_NONE, 'Overwrite the destination file if it already exists');
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
		$status = $input->getOption('status');

		// Check if the source file exists or is empty.
		if (!file_exists($source) || $this->isEmpty($source)) throw new \RuntimeException("You are trying to open an invalid file. Try with another one.");

		// Unless --force is set, check if destination file already exists.
		if (! $input->getOption('force')) {
			$this->verifyDestinationDoesntExist($destination);
		}

		$csv = Reader::createFromPath($source);

		// Set a delimiter for the CSV and check if it is the right one for the file.
		$delimiter = $input->getOption('delimiter');
		$this->validateDelimiter($delimiter);
		$csv->setDelimiter($this->delimiters[$delimiter]);
		$this->checkForBadDelimiter($csv);

		// Instantiate the ISBN parser and check if the parser finds columns containing ISBNS.
		$parser = IsbnParser::make();
		$first25Rows = $csv->setOffset(1)->setLimit(25)->fetchAll();
		if (! $indexes = $parser->findAllIndexes($first25Rows)) throw new \RuntimeException("No ISBNs found. Try to use a different delimiter or load another file.");

		$output->writeln(sprintf('<info>Found %s column(s) containing ISBNs.</info>', count($indexes)));
		$output->writeln(sprintf('Processing ISBNs...', count($indexes)));

		// Create a new progress bar.
		$progress = new ProgressBar($output);
		$progress->setRedrawFrequency(100);
		$progress->start();

		$identifiersCount = 0;
		$rowsCount = 0;
		foreach ($csv as $row) {
			$handle = fopen($destination, 'a');
			$identifiers = $parser->collectIdentifiers($row, $indexes);
			foreach ($identifiers as $identifier) {
				$line = [$identifier, $status];
				fputcsv($handle, $line, '	');

				$progress->advance();
				$identifiersCount++;
			}
			$rowsCount++;
			fclose($handle);
		}

		$progress->finish();
		$output->writeln("");
		$output->writeln("{$rowsCount} rows processed succesfully, {$identifiersCount} identifiers found.");

		// Remove duplicate identifiers.
		$output->writeln("Removing duplicates... ");
		$uniqueIdentifiers = $this->removeDuplicates($destination);
		$output->writeln("<info>Done: {$uniqueIdentifiers} unique identifiers saved.</info>");

		// Confirm the result of the operation.
		$output->writeln("You can upload this file to the dataloader: <info>{$destination}.</info>");
	}
}
