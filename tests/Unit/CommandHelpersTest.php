<?php

use PHPUnit\Framework\TestCase;

class CommandHelpersTest extends TestCase
{
    public function setUp()
    {
        // Any implementation of Dataloader\Command.
        $this->command = new Dataloader\IsbnCommand();
    }

    /** @test */
    public function autodetect_returns_the_correct_delimiter()
    {
        $this->assertEquals("\t", $this->command->autodetectDelimiter('tests/data/ebooks.tsv'));
        $this->assertEquals(";", $this->command->autodetectDelimiter('tests/data/journals2.csv'));
    }

    /** @test */
    public function autodetect_throws_an_exception_if_the_delimiter_is_unknown()
    {
        $this->expectException('RuntimeException');

        $this->command->autodetectDelimiter('tests/data/journals.colondelimited.csv');
    }

    /** @test */
    public function autodetect_throws_an_exception_if_the_file_passed_is_empty()
    {
        $this->expectException('RuntimeException');
        
        $this->command->autodetectDelimiter('tests/data/empty.tsv');
    }
}
