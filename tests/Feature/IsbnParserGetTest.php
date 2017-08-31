<?php

use League\Csv\Reader;
use PHPUnit\Framework\TestCase;
use Dataloader\Parsers\IsbnParser;

class IsbnParserGetTest extends TestCase
{
    public function setUp()
    {
        $this->csv = Reader::createFromPath('tests/data/ebooks.tsv');
        $this->csv->setDelimiter("\t");

        $this->parser = IsbnParser::make();
        $rows = $this->csv->setOffset(1)->setLimit(10)->fetchAll();;
        $this->columns = $this->parser->findAllIndexes($rows);
    }

    /** @test */
    public function it_collects_all_isbns_from_a_single_row_within_the_columns_provided()
    {
        $isbnsInRowTwo = [
            '9781905050352',
            '9781280480560',
            '9781905050840'
        ];

        $isbnsInRowFour = [
            '9780198774495',
            '9780198774501',
            '9780191525063',
            '9780191596476',
            '9781282052536'
        ];

        $isbnsFoundInRowTwo = $this->parser->collectIdentifiers($this->csv->fetchOne(2), $this->columns);
        $this->assertCount(3, array_intersect($isbnsInRowTwo, $isbnsFoundInRowTwo));

        $isbnsFoundInRowFour = $this->parser->collectIdentifiers($this->csv->fetchOne(4), $this->columns);
        $this->assertCount(5, array_intersect($isbnsInRowFour, $isbnsFoundInRowFour));
    }

    /** @test */
    public function it_returns_an_empty_array_if_the_columns_passed_do_not_exist()
    {
        $wrongColumns = ['997', '998', '999'];

        $this->assertEquals([], $this->parser->collectIdentifiers($this->csv->fetchOne(1), $wrongColumns));
    }

    /** @test */
    public function it_returns_an_empty_array_if_no_arguments_are_passed()
    {
        $this->assertEquals([], $this->parser->collectIdentifiers());
    }


}
