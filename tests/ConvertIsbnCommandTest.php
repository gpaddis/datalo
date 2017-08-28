<?php

use PHPUnit\Framework\TestCase;
use Dataloader\ConvertIsbnCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class ConvertIsbnCommandTest extends TestCase
{
	public function setUp()
	{
		$this->application = new Application();
		$this->application->add(new ConvertIsbnCommand());

		$this->command = $this->application->find('convert:isbn');
		$this->commandTester = new CommandTester($this->command);

		$this->unique = md5(date(DATE_RFC2822));
	}

	/** @test */
	public function it_converts_a_tsv_ebook_list()
	{
		$this->commandTester->execute(array(
			'command'  => $this->command->getName(),

            // pass arguments to the helper
			'source' => 'tests/data/ebscotabdelimited.tsv',
			'destination' => 'tests/data/something.txt',
			'--delimiter' => 'tab',
			'--force' => true
			));

        // the output of the command in the console
		$output = $this->commandTester->getDisplay();
		$this->assertContains('processed succesfully', $output);
		$this->assertContains('239', $output);
	}

	/** @test */
	public function it_throws_a_runtime_exception_if_the_delimiter_is_incorrect()
	{
		$this->expectException('RuntimeException');

		$this->commandTester->execute(array(
			'command'  => $this->command->getName(),

            // pass arguments to the helper
			'source' => 'tests/data/ebscotabdelimited.tsv',
			'destination' => 'tests/data/output.txt',
			'--delimiter' => 'comma',
			));
	}

		/** @test */
	public function it_throws_an_invalid_argument_exception_if_the_delimiter_is_not_allowed()
	{
		$this->expectException('InvalidArgumentException');

		$this->commandTester->execute(array(
			'command'  => $this->command->getName(),

            // pass arguments to the helper
			'source' => 'tests/data/ebscotabdelimited.tsv',
			'destination' => 'tests/data/output.txt',
			'--delimiter' => 'colon',
			));
	}

	/** @test */
	public function it_throws_an_exception_if_the_source_file_is_empty()
	{
		$this->expectException('RuntimeException');

		$this->commandTester->execute(array(
			'command'  => $this->command->getName(),

            // pass arguments to the helper
			'source' => 'tests/data/empty.tsv',
			'destination' => 'tests/data/output.txt',
			'--delimiter' => 'colon',
			));
	}

	/** @test */
	public function it_throws_an_exception_if_the_source_file_does_not_exists()
	{
		$this->expectException('RuntimeException');

		$this->commandTester->execute(array(
			'command'  => $this->command->getName(),
			'source' => 'tests/data/nonexisting.tsv',
			'destination' => 'tests/data/output.txt',
			'--delimiter' => 'colon',
			));
	}

	/** @test */
	public function it_throws_an_exception_if_the_destination_file_already_exists_and_force_is_not_set()
	{
		$this->expectException('RuntimeException');

		$this->commandTester->execute(array(
			'command'  => $this->command->getName(),
			'source' => 'tests/data/ebscotabdelimited.tsv',
			'destination' => 'tests/data/ebscotabdelimited.tsv',
			'--delimiter' => 'tab',
			));
	}

	// TODO: test status
}
