<?php

use League\Csv\Reader;
use PHPUnit\Framework\TestCase;
use Dataloader\Parsers\IsbnParser;
use Dataloader\Validators\IsbnValidator;

class IsbnParserLocateTest extends TestCase
{
    public function setUp()
    {
        $this->csv = Reader::createFromPath('tests/data/ebscotabdelimited.tsv');
        $this->csv->setDelimiter("\t");

        $this->validator = IsbnValidator::make();
    }

    /** @test */
    public function it_locates_the_columns_containing_valid_ISBNs_in_a_single_row()
    {
        $row = $this->csv->fetchOne(4);

        $columns = IsbnParser::analyzeRow($row, $this->validator);

        $this->assertEquals([14, 15], $columns);
    }

    /** @test */
    public function it_returns_an_empty_array_if_the_row_is_empty()
    {
        $row = '';

        $columns = IsbnParser::analyzeRow($row, $this->validator);

        $this->assertEquals([], $columns);
    }

    /** @test */
    public function it_collects_all_columns_containing_ISBNs_over_multiple_rows()
    {
        $columns = IsbnParser::findColumns($this->csv, $this->validator);

        $this->assertEquals([14, 15, 16, 17, 18, 19, 20, 21], $columns);
    }
}
