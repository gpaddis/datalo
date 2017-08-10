<?php

use PHPUnit\Framework\TestCase;
use Dataloader\Validators\IsbnValidator;

class IsbnTest extends TestCase
{
    /** @test */
    public function a_correct_isbn_passes_validation()
    {
        $this->assertTrue(IsbnValidator::make('978-0-415-27844-7')->isValid());
    }

    /** @test */
    public function a_wrong_isbn_does_not_pass_validation()
    {
        $this->assertFalse(IsbnValidator::make('978-0-415-27844-1')->isValid());
    }

    /** @test */
    public function a_random_text_string_does_not_pass_validation()
    {
        $this->assertFalse(IsbnValidator::make('foobarbaz')->isValid());
    }

    /** @test */
    public function a_string_of_10_or_13_characters_does_not_pass_validation()
    {
        $this->assertFalse(IsbnValidator::make('abcdefghij')->isValid());
        $this->assertFalse(IsbnValidator::make('abcdefghijklm')->isValid());
    }

    /** @test */
    public function it_returns_the_isbn()
    {
        $isbn = IsbnValidator::make('978-0-415-27844-7');

        $this->assertEquals('9780415278447', $isbn->get());
    }
}
