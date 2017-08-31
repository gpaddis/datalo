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
    public function it_returns_true_if_column_numbers_match()
    {
        $header = ['a', 'b', 'c', 'd'];
        $firstRow = [1, 2, 3, 4];
        $this->assertTrue($this->command->matchNumberOfColumns($header, $firstRow));
    }

    /** @test */
    public function it_fails_if_column_numbers_dont_match()
    {
        $header = ['a', 'b', 'c', 'd'];
        $firstRow = [1, 2, 3, 4, 5];
        $this->assertFalse($this->command->matchNumberOfColumns($header, $firstRow));
    }

    /** @test */
    public function it_fails_if_at_least_one_row_has_only_one_column()
    {
        $header = ['a', 'b'];
        $firstRow = [1];
        $this->assertFalse($this->command->matchNumberOfColumns($header, $firstRow));
    }
}
