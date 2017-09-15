<?php

use Dataloader\IsbnCommand;
use League\Csv\Reader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class IsbnCommandTest extends TestCase
{
    public function setUp()
    {
        $this->application = new Application();
        $this->application->add(new IsbnCommand());

        $this->command = $this->application->find('isbn');
        $this->commandTester = new CommandTester($this->command);

        $this->csvSource = Reader::createFromPath('tests/data/ebooks.tsv');
        $this->csvSource->setDelimiter("\t");

        $this->csvDestination = Reader::createFromPath('tests/data/output.txt');
        $this->csvDestination->setDelimiter("\t");
    }

    /** @test */
    public function it_converts_a_tsv_ebook_list()
    {
        $this->commandTester->execute([
            'command'     => $this->command->getName(),
            'source'      => 'tests/data/ebooks.tsv',
            'destination' => 'tests/data/output.txt',
            '--force'     => true,
            ]);

        // the output of the command in the console
        $output = $this->commandTester->getDisplay();
        $this->assertContains('processed succesfully', $output);
        $this->assertContains('239', $output);
    }

    /** @test */
    public function it_throws_an_exception_if_the_source_file_is_empty()
    {
        $this->expectException('RuntimeException');

        $this->commandTester->execute([
            'command'     => $this->command->getName(),
            'source'      => 'tests/data/empty.tsv',
            'destination' => 'tests/data/output.txt',
            ]);
    }

    /** @test */
    public function it_throws_an_exception_if_the_source_file_does_not_exists()
    {
        $this->expectException('RuntimeException');

        $this->commandTester->execute([
            'command'     => $this->command->getName(),
            'source'      => 'tests/data/nonexisting.tsv',
            'destination' => 'tests/data/output.txt',
            ]);
    }

    /** @test */
    public function it_throws_an_exception_if_the_destination_file_already_exists_and_force_is_not_set()
    {
        $this->expectException('RuntimeException');

        $this->commandTester->execute([
            'command'     => $this->command->getName(),
            'source'      => 'tests/data/ebooks.tsv',
            'destination' => 'tests/data/ebooks.tsv',
            ]);
    }

    /** @test */
    public function it_can_set_a_custom_status_flag_in_the_second_column()
    {
        $this->commandTester->execute([
            'command'     => $this->command->getName(),
            'source'      => 'tests/data/ebooks.tsv',
            'destination' => 'tests/data/output.txt',
            '--force'     => true,
            '--status'    => 'INACTIVE',
            ]);

        // the output of the command in the console
        $output = $this->commandTester->getDisplay();
        $this->assertContains('processed succesfully', $output);

        $anyLine = $this->csvDestination->fetchOne(rand(1, 20));
        $this->assertContains('INACTIVE', $anyLine);
    }

    /** @test */
    public function it_saves_only_one_column_of_identifiers_if_status_equals_none()
    {
        $this->commandTester->execute([
            'command'     => $this->command->getName(),
            'source'      => 'tests/data/ebooks.tsv',
            'destination' => 'tests/data/output.txt',
            '--force'     => true,
            '--status'    => 'NONE',
            ]);

        // the output of the command in the console
        $output = $this->commandTester->getDisplay();
        $this->assertContains('processed succesfully', $output);

        $anyLine = $this->csvDestination->fetchOne(rand(1, 30));

        $this->assertCount(1, $anyLine);
        $this->assertFalse(in_array('ACTIVE', $anyLine));
    }
}
