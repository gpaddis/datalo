<?php

use PHPUnit\Framework\TestCase;
use Dataloader\Parsers\IsbnParser;

class IsbnParserSplitTest extends TestCase
{
    /** @test */
    public function it_splits_a_field_containing_multiple_isbns_to_an_array()
    {
        // The following is an ISBN field from an EBSCO title list
        $isbns = '978-0-19-152190-4|978-0-19-163894-7|978-0-19-171547-1|978-1-281-97836-3';

        $parsedIsbns = IsbnParser::split($isbns);

        $this->assertEquals([
            '978-0-19-152190-4',
            '978-0-19-163894-7',
            '978-0-19-171547-1',
            '978-1-281-97836-3'
        ], $parsedIsbns);
    }

    /** @test */
    public function it_accepts_multiple_separators()
    {
        // The following is an actual ISBN field exported from WinIBW
        $isbns = '1628250763 = 978-1-62825-076-3; 1680157221 = 978-1-68015-722-2';

        $parsedIsbns = IsbnParser::split($isbns);

        $this->assertEquals([
            '1628250763',
            '978-1-62825-076-3',
            '1680157221',
            '978-1-68015-722-2'
        ], $parsedIsbns);
    }

    /** @test */
    public function it_returns_an_array_if_we_pass_a_single_isbn()
    {
        $parsedIsbn = IsbnParser::split('978-1-62825-076-3');

        $this->assertEquals(['978-1-62825-076-3'], $parsedIsbn);
    }

    /** @test */
    public function it_returns_an_empty_array_if_we_pass_nothing()
    {
        $this->assertEquals([], IsbnParser::split('', ''));
        $this->assertEquals([], IsbnParser::split(' ', ' '));
        $this->assertEquals([], IsbnParser::split());
    }

    /** @test */
    public function it_accepts_custom_separators() // TODO: check for conflicts with REGEX
    {
        $isbns = '978-0-19-152190-4$978-0-19-163894-7';

        $this->assertEquals(['978-0-19-152190-4', '978-0-19-163894-7'], IsbnParser::split($isbns, '$'));
    }

    /** @test */
    public function it_uses_the_default_separators_if_we_pass_an_empty_separator_parameter()
    {
        $isbns = '978-0-19-152190-4|978-0-19-163894-7';

        $this->assertEquals(['978-0-19-152190-4', '978-0-19-163894-7'], IsbnParser::split($isbns, ''));
    }
}
