<?php namespace Dataloader;

use RuntimeException;
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
		->addArgument('source', InputArgument::REQUIRED, 'The source file that you want to process.')
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
		if (!file_exists($filename) || $this->isEmpty($filename)) throw new RuntimeException("You are trying to open an invalid file. Try with another one.");
		
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
		if (! $indexes = $parser->findAllIndexes($first25Rows)) throw new RuntimeException("No ISBNs found. Try to use a different delimiter or load another file.");

		$output->writeln(sprintf('<info>Found %s column(s) containing ISBNs.</info>', count($indexes)));
		$output->writeln(sprintf('Processing file...', count($indexes)));

		// Extract all identifiers and save them in the destination file.
		$destination = $input->getArgument('destination');
		$status = $input->getOption('status');
		$handle = fopen($destination, 'a');

		// Create a new progress bar.
		$progress = new ProgressBar($output);
		$progress->setRedrawFrequency(100);
		$progress->start();

		$i = 0;
		$count = 0;
		while ($row = $csv->fetchOne($i)) {
			$identifiers = $parser->collectIdentifiers($row, $indexes);
			foreach ($identifiers as $identifier) {
				$line = [$identifier, $status];
				fputcsv($handle, $line, '	');
				$progress->advance();
				$count++;
			}
			$i++;
		}
		fclose($handle);

		// Confirm the result of the operation.
		$progress->finish();
		$output->writeln("");
		$output->writeln("{$count} identifiers processed succesfully.");
		$output->writeln("The file for the dataloader was saved here: <info>{$destination}.</info>");

	}

	/**
	 * Check if the delimiter entered is in the white list.
	 *
	 * @param  string $delimiter
	 * @return boolean | RuntimeException
	 */
	protected function validateDelimiter($delimiter)
	{
		if (! array_key_exists($delimiter, $this->delimiters)) {
			$delimiters = implode('", "', array_keys($this->delimiters));
			throw new \InvalidArgumentException("You entered an invalid delimiter. Try with \"{$delimiters}\".");
		}
	}

	/**
	 * If there's only one column, this means the delimiter is not correct.
	 * 
	 * @return void
	 */
	protected function checkForBadDelimiter($csv)
	{
		if (count($csv->fetchOne(1)) <= 1) throw new RuntimeException("You didn't choose the appropriate delimiter. Try with another one.");
	}

	/**
	 * Returns true if the file is empty.
	 * 
	 * @param  [type]  $file [description]
	 * @return boolean
	 */
	protected function isEmpty($file)
	{
		return filesize($file) == 0;
	}
}
