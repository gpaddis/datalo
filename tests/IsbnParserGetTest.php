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
    public function it_extracts_an_array_of_isbns_within_the_columns_provided()
    {
        $rowsToCheck = 5;
        $isbnsToFind = [
            '9781606929735',
            '9780511303388',
            '9781608762941',
            '9781905050352',
            '9781280480560',
            '9781905050840',
            '9789812381217',
            '9781281929358',
            '9789812776792',
            '9780198774495',
            '9780198774501',
            '9780191525063',
            '9780191596476',
            '9781282052536'
        ];

        $foundIsbns = $this->parser->getIdentifiers($this->columns, $rowsToCheck);

        $this->assertEquals($isbnsToFind, $foundIsbns);
    }
}
