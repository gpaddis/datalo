<?php

use PHPUnit\Framework\TestCase;
use Dataloader\Validators\IssnValidator;

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
    public function a_correct_issn_passes_validation()
    {
        $this->assertTrue($this->validator->validate('0017-8012'));
        $this->assertTrue($this->validator->validate('0098-9258'));
        $this->assertTrue($this->validator->validate('0717-344X'));
        $this->assertTrue($this->validator->validate('17404282'));
    }

    /** @test */
    public function a_wrong_issn_does_not_pass_validation()
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
}
