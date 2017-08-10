<?php

use League\Csv\Reader;
use PHPUnit\Framework\TestCase;
use Dataloader\Parsers\IsbnParser;

class IsbnParserLocateTest extends TestCase
{
    public function setUp()
    {
        $this->csv = Reader::createFromPath('tests/data/ebscotabdelimited.tsv');
        $this->csv->setDelimiter("\t");

        $this->parser = IsbnParser::make($this->csv);
    }

    /** @test */
    public function it_locates_the_columns_containing_valid_ISBNs_in_a_single_row()
    {
        // $row = $this->csv->fetchOne(4);

        $columns = $this->parser->analyzeRow(4);

        $this->assertEquals([14, 15], $columns);
    }

    /** @test */
    public function it_returns_an_empty_array_if_the_row_does_not_contain_identifiers()
    {
        $columns = $this->parser->analyzeRow(0);

        $this->assertEquals([], $columns);
    }

    /** @test */
    public function it_can_be_called_without_arguments_and_defaults_to_zero()
    {
        $columns = $this->parser->analyzeRow();

        $this->assertEquals([], $columns);
    }

    /** @test */
    public function it_collects_all_columns_containing_ISBNs_over_multiple_rows()
    {
        $columns = $this->parser->findIndexes();

        $this->assertEquals([14, 15, 16, 17, 18, 19, 20, 21], $columns);
    }

    /** @test */
    public function it_accepts_a_custom_number_of_iterations()
    {
        $columns = $this->parser->findIndexes(15);

        $this->assertEquals([14, 15, 16, 17, 18, 19, 20, 21], $columns);
    }
}
