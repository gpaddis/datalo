<?php

use Dataloader\Splitters\Splitter;
use PHPUnit\Framework\TestCase;

class IssnSplitterTest extends TestCase
{
    public function setUp()
    {
        $this->splitter = new Splitter();
    }

    /** @test */
    public function it_splits_a_field_containing_multiple_isbns_to_an_array()
    {
        $issn = '0017-8012|0098-9258|0717-344X';

        $parsedIssns = $this->splitter->split($issn);

        $this->assertEquals([
            '0017-8012',
            '0098-9258',
            '0717-344X',
        ], $parsedIssns);
    }

    /** @test */
    public function it_recognizes_multiple_separators()
    {
        $issn = '0017-8012; Issn: 0717-344X';

        $parsedIsbns = $this->splitter->split($issn);

        $this->assertEquals([
            '0017-8012',
            '0717-344X',
        ], $parsedIsbns);
    }

    /** @test */
    public function it_returns_an_array_if_we_pass_a_single_issn()
    {
        $parsedIssn = $this->splitter->split('0717-344X');

        $this->assertEquals(['0717-344X'], $parsedIssn);
    }

    /** @test */
    public function it_returns_an_empty_array_if_we_pass_nothing()
    {
        $this->assertEquals([], $this->splitter->split(''));
        $this->assertEquals([], $this->splitter->split(' '));
        $this->assertEquals([], $this->splitter->split());
    }
}
