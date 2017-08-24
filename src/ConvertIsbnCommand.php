<?php namespace Dataloader;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConvertIsbnCommand extends Command
{
	public function configure()
	{
		$this->setName('convert:isbn')
		->setDescription('Generate a list of ISBNs.')
		->addArgument('source', InputArgument::REQUIRED, 'The source file that you want to convert.')
		->addOption('separator', null, InputOption::VALUE_OPTIONAL, 'Set the source file separator (comma, tab, semicolon). Default: comma.', 'comma');
	}

	public function execute(InputInterface $input, OutputInterface $output)
	{
		$message = sprintf('You selected the file: %s. Separator: %s', $input->getArgument('source'), $input->getOption('separator'));

		$output->writeln("<info>{$message}</info>");
	}
}