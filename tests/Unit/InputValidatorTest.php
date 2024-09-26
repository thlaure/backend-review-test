<?php

use App\Service\InputValidator;
use PHPUnit\Framework\TestCase;

class InputValidatorTest extends TestCase
{
    private InputValidator $inputValidator;

    protected function setUp(): void
    {
        $this->inputValidator = new InputValidator();
    }

    public function testValidateDateSuccess(): void
    {
        $this->assertTrue($this->inputValidator->validateDate('2022-12-31'));
    }

    public function testValidateDateFailOtherDateFormat(): void
    {
        $this->assertFalse($this->inputValidator->validateDate('31-12-2022'));
    }

    public function testValidateDateFailWrongFormat(): void
    {
        $this->assertFalse($this->inputValidator->validateDate('test'));
    }
}
