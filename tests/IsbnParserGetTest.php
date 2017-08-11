<?php

use League\Csv\Reader;
use PHPUnit\Framework\TestCase;
use Dataloader\Parsers\IsbnParser;

class IsbnParserGetTest extends TestCase
{
    public function setUp()
    {
        $this->csv = Reader::createFromPath('tests/data/ebscotabdelimited.tsv');
        $this->csv->setDelimiter("\t");

        $this->parser = IsbnParser::make($this->csv);

        $this->columns = $this->parser->findIndexes();
    }

    /** @test */
    public function it_extracts_an_array_of_isbns_from_a_single_row_within_the_columns_provided()
    {
        $isbnsToFind = [
        '9781606929735',
        '9780511303388',
        '9781608762941'
        ];

        $row = 1;
        $isbnsFound = $this->parser->getIdentifiers($this->columns, $row);

        $this->assertEquals($isbnsToFind, $isbnsFound);
    }

    /** @test */
    public function it_returns_an_empty_array_if_the_columns_passed_do_not_exist()
    {
        $wrongColumns = ['997', '998', '999'];

        $this->assertEquals([], $this->parser->getIdentifiers($wrongColumns, 1));
    }

    /** @test */
    public function it_returns_an_empty_array_if_no_arguments_are_passed()
    {
        $this->assertEquals([], $this->parser->getIdentifiers());
    }
}
