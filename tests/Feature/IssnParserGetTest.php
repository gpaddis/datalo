<?php

use Dataloader\Parsers\IssnParser;
use League\Csv\Reader;
use PHPUnit\Framework\TestCase;

class IssnParserGetTest extends TestCase
{
    public function setUp()
    {
        $this->csv = Reader::createFromPath('tests/data/journals.csv');
        $this->csv->setDelimiter("\t");

        $this->parser = IssnParser::make();
        $rows = $this->csv->setOffset(1)->setLimit(25)->fetchAll();
        $this->columns = $this->parser->findAllIndexes($rows);
    }

    /** @test */
    public function it_collects_all_ISSNs_from_a_single_row_within_the_columns_provided()
    {
        $issnsInRowEleven = [
            '1435246X',
            '16139178',
        ];

        $issnsInRowThirteen = [
            '09277099',
            '15729974',
        ];

        $issnsFoundInRowEleven = $this->parser->collectIdentifiers($this->csv->fetchOne(11), $this->columns);
        $this->assertCount(2, array_intersect($issnsInRowEleven, $issnsFoundInRowEleven));

        $issnsFoundInRowThirteen = $this->parser->collectIdentifiers($this->csv->fetchOne(13), $this->columns);
        $this->assertCount(2, array_intersect($issnsInRowThirteen, $issnsFoundInRowThirteen));
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
