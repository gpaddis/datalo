<?php

use League\Csv\Reader;
use Dataloader\IssnCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class IssnCommandTest extends TestCase
{
    public function setUp()
    {
        $this->application = new Application();
        $this->application->add(new IssnCommand());

        $this->command = $this->application->find('issn');
        $this->commandTester = new CommandTester($this->command);

        $this->csvSource = Reader::createFromPath('tests/data/journals.csv');
        $this->csvSource->setDelimiter("\t");

        $this->csvDestination = Reader::createFromPath('tests/data/output_journals.txt');
        $this->csvDestination->setDelimiter("\t");
    }

    /** @test */
    public function it_converts_a_tsv_journals_list()
    {
        $this->commandTester->execute(array(
            'command'  => $this->command->getName(),
            'source' => 'tests/data/journals.csv',
            'destination' => 'tests/data/output_journals.txt',
            '--delimiter' => 'tab',
            '--force' => true
            ));

        // the output of the command in the console
        $output = $this->commandTester->getDisplay();
        $this->assertContains('processed succesfully', $output);
        $this->assertContains('90', $output);
    }

    /** @test */
    public function it_throws_a_runtime_exception_if_the_delimiter_is_incorrect()
    {
        $this->expectException('RuntimeException');

        $this->commandTester->execute(array(
            'command'  => $this->command->getName(),
            'source' => 'tests/data/journals.csv',
            'destination' => 'tests/data/somefile.txt',
            '--delimiter' => 'comma',
            ));
    }

        /** @test */
    public function it_throws_an_invalid_argument_exception_if_the_delimiter_is_not_allowed()
    {
        $this->expectException('InvalidArgumentException');

        $this->commandTester->execute(array(
            'command'  => $this->command->getName(),
            'source' => 'tests/data/journals.csv',
            'destination' => 'tests/data/somefile.txt',
            '--delimiter' => 'space',
            ));
    }

    /** @test */
    public function it_throws_an_exception_if_the_source_file_is_empty()
    {
        $this->expectException('RuntimeException');

        $this->commandTester->execute(array(
            'command'  => $this->command->getName(),
            'source' => 'tests/data/empty.tsv',
            'destination' => 'tests/data/output_journals.txt',
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
            'destination' => 'tests/data/output_journals.txt',
            '--delimiter' => 'colon',
            ));
    }

    /** @test */
    public function it_throws_an_exception_if_the_destination_file_already_exists_and_force_is_not_set()
    {
        $this->expectException('RuntimeException');

        $this->commandTester->execute(array(
            'command'  => $this->command->getName(),
            'source' => 'tests/data/journals.csv',
            'destination' => 'tests/data/journals.csv',
            '--delimiter' => 'tab',
            ));
    }

    /** @test */
    public function it_can_set_a_custom_status_flag_in_the_second_column()
    {
        $this->commandTester->execute(array(
            'command'  => $this->command->getName(),
            'source' => 'tests/data/journals.csv',
            'destination' => 'tests/data/output_journals.txt',
            '--delimiter' => 'tab',
            '--force' => true,
            '--status' => 'INACTIVE'
            ));

        // the output of the command in the console
        $output = $this->commandTester->getDisplay();
        $this->assertContains('processed succesfully', $output);

        $anyLine = $this->csvDestination->fetchOne(24);
        $this->assertContains('INACTIVE', $anyLine);
    }
}
