<?php

use Dataloader\IssnCommand;
use League\Csv\Reader;
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
        $this->commandTester->execute([
            'command'     => $this->command->getName(),
            'source'      => 'tests/data/journals.csv',
            'destination' => 'tests/data/output_journals.txt',
            '--force'     => true,
            ]);

        // the output of the command in the console
        $output = $this->commandTester->getDisplay();
        $this->assertContains('processed succesfully', $output);
        $this->assertContains('90', $output);
    }

    /** @test */
    public function it_throws_an_exception_if_the_source_file_is_empty()
    {
        $this->expectException('RuntimeException');

        $this->commandTester->execute([
            'command'     => $this->command->getName(),
            'source'      => 'tests/data/empty.tsv',
            'destination' => 'tests/data/output_journals.txt',
            ]);
    }

    /** @test */
    public function it_throws_an_exception_if_the_delimiter_is_not_detected_automatically()
    {
        $this->expectException('RuntimeException');

        $this->commandTester->execute([
            'command'     => $this->command->getName(),
            'source'      => 'tests/data/journals.colondelimited.csv',
            'destination' => 'tests/data/output_journals.txt',
            ]);
    }

    /** @test */
    public function it_accepts_a_custom_delimiter()
    {
        $this->commandTester->execute([
            'command'     => $this->command->getName(),
            'source'      => 'tests/data/journals.colondelimited.csv',
            'destination' => 'tests/data/output_journals.txt',
            '--delimiter' => ':',
            '--force'     => true,
            ]);

        $output = $this->commandTester->getDisplay();
        $this->assertContains('processed succesfully', $output);
        $this->assertContains('90', $output);
    }

    /** @test */
    public function it_throws_an_exception_if_the_source_file_does_not_exists()
    {
        $this->expectException('RuntimeException');

        $this->commandTester->execute([
            'command'     => $this->command->getName(),
            'source'      => 'tests/data/nonexisting.tsv',
            'destination' => 'tests/data/output_journals.txt',
            ]);
    }

    /** @test */
    public function it_throws_an_exception_if_the_destination_file_already_exists_and_force_is_not_set()
    {
        $this->expectException('RuntimeException');

        $this->commandTester->execute([
            'command'     => $this->command->getName(),
            'source'      => 'tests/data/journals.csv',
            'destination' => 'tests/data/journals.csv',
            ]);
    }

    /** @test */
    public function it_can_set_a_custom_status_flag_in_the_second_column()
    {
        $this->commandTester->execute([
            'command'     => $this->command->getName(),
            'source'      => 'tests/data/journals.csv',
            'destination' => 'tests/data/output_journals.txt',
            '--force'     => true,
            '--status'    => 'INACTIVE',
            ]);

        // the output of the command in the console
        $output = $this->commandTester->getDisplay();
        $this->assertContains('processed succesfully', $output);

        $anyLine = $this->csvDestination->fetchOne(24);
        $this->assertContains('INACTIVE', $anyLine);
    }
}
