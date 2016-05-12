<?php

namespace Mooti\Test\PHPUnit\Validator\Unit\TypeValidator;

use Mooti\Validator\Exception\DataValidationException;
use Mooti\Validator\TypeValidator\StringValidator;

class StringValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function validateSucceeds()
    {
        $constraints = [];

        $data = 'test';

        $typeValidator = new StringValidator;

        $typeValidator->validate($constraints, $data);
    }

    /**
     * @test
     */
    public function validateWithLengthSucceeds()
    {
        $constraints = [
            'length' => [1,20]
        ];

        $data = 'test';

        $typeValidator = $this->getMockBuilder(StringValidator::class)
            ->disableOriginalConstructor()
            ->setMethods(['validateLength'])
            ->getMock();

        $typeValidator->expects(self::once())
            ->method('validateLength')
            ->with(
                self::equalTo($data),
                self::equalTo(1),
                self::equalTo(20)
            );

        $typeValidator->validate($constraints, $data);
    }

    /**
     * @test
     * @expectedException Mooti\Validator\Exception\DataValidationException
     * @expectedExceptionMessage This value must be a string
     */
    public function validateThrowsDataValidationException()
    {
        $constraints = [];

        $data = 1;

        $typeValidator = new StringValidator;

        $typeValidator->validate($constraints, $data);
    }

    /**
     * @test
     * @expectedException Mooti\Validator\Exception\InvalidRuleException
     * @expectedExceptionMessage The length property needs to have two members
     */
    public function validateToFewMembersThrowsInvalidRuleException()
    {
        $constraints = [
            'length' => [1]
        ];

        $data = 'hello';

        $typeValidator = new StringValidator;

        $typeValidator->validate($constraints, $data);
    }

    /**
     * @test
     * @expectedException Mooti\Validator\Exception\InvalidRuleException
     * @expectedExceptionMessage The length property needs to have two members
     */
    public function validateToManyMembersThrowsInvalidRuleException()
    {
        $constraints = [
            'length' => [1,2,3]
        ];

        $data = 'hello';

        $typeValidator = new StringValidator;

        $typeValidator->validate($constraints, $data);
    }

    /**
     * @test
     * @expectedException Mooti\Validator\Exception\DataValidationException
     * @expectedExceptionMessage This value must have a length less than or equal to 3
     */
    public function validateLengthTooLargeThrowsDataValidationException()
    {
        $min = 2;
        $max = 3;

        $data = '1234';

        $typeValidator = new StringValidator;

        $typeValidator->validateLength($data, $min, $max);
    }

    /**
     * @test
     * @expectedException Mooti\Validator\Exception\DataValidationException
     * @expectedExceptionMessage This value must have a length of at least 2
     */
    public function validateLengthTooSmallThrowsDataValidationException()
    {
        $min = 2;
        $max = 3;

        $data = '1';

        $typeValidator = new StringValidator;

        $typeValidator->validateLength($data, $min, $max);
    }

    /**
     * @test
     */
    public function validateLengthSucceeds()
    {
        $min = 1;
        $max = 3;

        $data = '12';

        $typeValidator = new StringValidator;

        $typeValidator->validateLength($data, $min, $max);
    }
}
