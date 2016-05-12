<?php

namespace Mooti\Test\PHPUnit\Validator\Unit\TypeValidator;

use Mooti\Validator\Exception\DataValidationException;
use Mooti\Validator\TypeValidator\NumberValidator;

class NumberValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function validateSucceeds()
    {
        $constraints = [
            'integer' => true
        ];

        $data = 1;

        $typeValidator = $this->getMockBuilder(NumberValidator::class)
            ->disableOriginalConstructor()
            ->setMethods(['validateInteger'])
            ->getMock();

        $typeValidator->expects(self::once(3))
            ->method('validateInteger')
            ->with(
                self::equalTo($data),
                self::equalTo(true)
            );

        $typeValidator->validate($constraints, $data);
    }

    /**
     * @test
     * @expectedException Mooti\Validator\Exception\DataValidationException
     * @expectedExceptionMessage This value must be a number
     */
    public function validateDataThrowsDataValidationException()
    {
        $constraints = [];

        $data = 'foobar';

        $typeValidator = new NumberValidator;

        $typeValidator->validate($constraints, $data);
    }

    /**
     * @test
     * @expectedException Mooti\Validator\Exception\DataValidationException
     * @expectedExceptionMessage This value must  be an integer
     */
    public function validateIntegerIsIntThrowsDataValidationException()
    {
        $data = 1.1;

        $typeValidator = new NumberValidator;

        $typeValidator->validateInteger($data, true);
    }

    /**
     * @test
     * @expectedException Mooti\Validator\Exception\DataValidationException
     * @expectedExceptionMessage This value must not be an integer
     */
    public function validateIntegerNotIntThrowsDataValidationException()
    {
        $data = 1;

        $typeValidator = new NumberValidator;

        $typeValidator->validateInteger($data, false);
    }

    /**
     * @test
     */
    public function validateIntegerIsIntSucceeds()
    {
        $data = 1;

        $typeValidator = new NumberValidator;

        $typeValidator->validateInteger($data, true);
    }

    /**
     * @test
     */
    public function validateIntegerNotIntSucceeds()
    {
        $data = 1.1;

        $typeValidator = new NumberValidator;

        $typeValidator->validateInteger($data, false);
    }
}
