<?php

use Dataloader\Splitters\Splitter;
use PHPUnit\Framework\TestCase;

class IsbnSplitterTest extends TestCase
{
    public function setUp()
    {
        $this->splitter = new Splitter();
    }

    /** @test */
    public function it_splits_a_field_containing_multiple_isbns_to_an_array()
    {
        // The following is an ISBN field from an EBSCO title list
        $isbns = '978-0-19-152190-4|978-0-19-163894-7|978-0-19-171547-1|978-1-281-97836-3';

        $parsedIsbns = $this->splitter->split($isbns);

        $this->assertEquals([
            '978-0-19-152190-4',
            '978-0-19-163894-7',
            '978-0-19-171547-1',
            '978-1-281-97836-3',
        ], $parsedIsbns);
    }

    /** @test */
    public function it_recognizes_multiple_separators()
    {
        // The following is an actual ISBN field exported from WinIBW
        $isbns = '1628250763 = 978-1-62825-076-3; 1680157221 = 978-1-68015-722-2';

        $parsedIsbns = $this->splitter->split($isbns);

        $this->assertEquals([
            '1628250763',
            '978-1-62825-076-3',
            '1680157221',
            '978-1-68015-722-2',
        ], $parsedIsbns);
    }

    /** @test */
    public function it_returns_an_array_if_we_pass_a_single_isbn()
    {
        $parsedIsbn = $this->splitter->split('978-1-62825-076-3');

        $this->assertEquals(['978-1-62825-076-3'], $parsedIsbn);
    }

    /** @test */
    public function it_returns_an_empty_array_if_we_pass_nothing()
    {
        $this->assertEquals([], $this->splitter->split(''));
        $this->assertEquals([], $this->splitter->split(' '));
        $this->assertEquals([], $this->splitter->split());
    }
}
