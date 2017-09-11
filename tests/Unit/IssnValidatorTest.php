<?php

use Dataloader\Validators\IssnValidator;
use PHPUnit\Framework\TestCase;

class IssnValidatorTest extends TestCase
{
    public function setUp()
    {
        $this->validator = IssnValidator::make();
    }

    /** @test */
    public function it_returns_a_valid_check_digit()
    {
        $this->assertEquals(2, $this->validator->checkDigit('0017-8012'));
        $this->assertEquals(8, $this->validator->checkDigit('0098-9258'));
        $this->assertEquals('X', $this->validator->checkDigit('0717-344X'));
        $this->assertEquals('X', $this->validator->checkDigit('0717344X'));
    }

    /** @test */
    public function it_returns_the_last_digit_on_the_right()
    {
        $this->assertEquals(2, $this->validator->lastDigit('0017-8012'));
        $this->assertEquals(8, $this->validator->lastDigit('0098-9258'));
        $this->assertEquals('X', $this->validator->lastDigit('0717-344X'));
    }

    /** @test */
    public function a_correct_ISSN_passes_validation()
    {
        $this->assertTrue($this->validator->validate('0017-8012'));
        $this->assertTrue($this->validator->validate('0098-9258'));
        $this->assertTrue($this->validator->validate('0717-344X'));
        $this->assertTrue($this->validator->validate('17404282'));
    }

    /** @test */
    public function a_wrong_ISSN_does_not_pass_validation()
    {
        $this->assertFalse($this->validator->validate('0098-9253'));
        $this->assertFalse($this->validator->validate('0098-925'));
        $this->assertFalse($this->validator->validate('0098-92588'));
        $this->assertFalse($this->validator->validate('abcd-efgh'));
    }

    /** @test */
    public function a_random_text_string_does_not_pass_validation()
    {
        $this->assertFalse($this->validator->validate('foobarbaz'));
    }

    /** @test */
    public function a_date_does_not_pass_validation()
    /*
     * This happens if we strip all dashes from a date string whose last digit, by coincidence,
     * corresponds to the check digit of a valid ISSN. It returns a false positive when
     * IssnParser::analyzeRow() checks which columns contain identifiers.
     */
    {
        $this->assertFalse($this->validator->validate('1984-01-01'));
    }

    /** @test */
    public function an_ISSN_within_a_number_string_does_not_pass_validation()
    {
        $this->assertFalse($this->validator->validate('00123232-0017-8012-987982-23213'));
        $this->assertFalse($this->validator->validate('001232320017801298798223213'));
    }
}
