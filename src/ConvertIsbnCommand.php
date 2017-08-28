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
		->setDescription('Generate a list of ISBNs.')
		->addArgument('source', InputArgument::REQUIRED, 'The delimiter-separated source file you want to process.')
		->addArgument('destination', InputArgument::REQUIRED, 'The name of the tab separated file for the SFX Dataloader.')
		->addOption('delimiter', null, InputOption::VALUE_REQUIRED, 'Set the delimiter for the source file (comma/tab/semicolon).', 'comma')
		->addOption('status', null, InputOption::VALUE_REQUIRED, 'Set the desired status for the portfolios (e.g. ACTIVE, INACTIVE).', 'ACTIVE');
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
		$filename = $input->getArgument('source');

		// Check if the file exists.
		if (!file_exists($filename) || $this->isEmpty($filename)) throw new \RuntimeException("You are trying to open an invalid file. Try with another one.");

		// Load the file and instantiate CSV Reader and IsbnParser.
		$csv = Reader::createFromPath($filename);

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

		// Extract all identifiers and save them in the destination file.
		$destination = $input->getArgument('destination');
		if (file_exists($destination)) throw new \RuntimeException('Destination file already exists. Please choose another file name.');

		$status = $input->getOption('status');

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

		// Confirm the result of the operation.
		$progress->finish();
		$output->writeln("");
		$output->writeln("{$rowsCount} rows processed succesfully, {$identifiersCount} identifiers found.");
		$output->writeln("The file for the dataloader was saved here: <info>{$destination}.</info>");
	}
}
