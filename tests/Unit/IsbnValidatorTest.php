<?php

use PHPUnit\Framework\TestCase;
use Dataloader\Validators\IsbnValidator;

class IsbnValidatorTest extends TestCase
{
    public function setUp()
    {
        $this->validator = IsbnValidator::make();
    }

    /** @test */
    public function a_correct_isbn_passes_validation()
    {
        $this->assertTrue($this->validator->validate('978-0-415-27844-7'));
    }

    /** @test */
    public function a_wrong_isbn_does_not_pass_validation()
    {
        $this->assertFalse($this->validator->validate('978-0-415-27844-1'));
    }

    /** @test */
    public function a_random_text_string_does_not_pass_validation()
    {
        $this->assertFalse($this->validator->validate('foobarbaz'));
    }

    /** @test */
    public function a_string_of_10_or_13_characters_does_not_pass_validation()
    {
        $this->assertFalse($this->validator->validate('abcdefghij'));
        $this->assertFalse($this->validator->validate('abcdefghijklm'));
    }
}
