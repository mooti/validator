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

        $typeValidator = new StringValidator;

        $typeValidator->validate($constraints, $data);
    }

    /**
     * @test
     */
    public function validateWithEnumSucceeds()
    {
        $constraints = [
            'enum' => ['foo','bar']
        ];

        $data = 'foo';

        $typeValidator = new StringValidator;

        $typeValidator->validate($constraints, $data);
    }

    /**
     * @test
     * @expectedException \Mooti\Validator\Exception\DataValidationException
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
     * @expectedException \Mooti\Validator\Exception\InvalidRuleException
     * @expectedExceptionMessage The length property of This value needs to have two members
     */
    public function validateLengthTooFewMembersThrowsInvalidRuleException()
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
     * @expectedException \Mooti\Validator\Exception\InvalidRuleException
     * @expectedExceptionMessage The length property of This value needs to have two members
     */
    public function validateLengthTooManyMembersThrowsInvalidRuleException()
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
     * @expectedException \Mooti\Validator\Exception\DataValidationException
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
     * @expectedException \Mooti\Validator\Exception\DataValidationException
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

    /**
     * @test
     * @expectedException \Mooti\Validator\Exception\InvalidRuleException
     * @expectedExceptionMessage The enum property of This value needs to have at least one member
     */
    public function validateEnumTooFewMembersThrowsInvalidRuleException()
    {
        $constraints = [
            'enum' => []
        ];

        $data = 'foo';

        $typeValidator = new StringValidator;

        $typeValidator->validate($constraints, $data);
    }

    /**
     * @test
     * @expectedException \Mooti\Validator\Exception\DataValidationException
     * @expectedExceptionMessage test is not an allowed value for This value. Allowed values are: foo, bar
     */
    public function validateEnumlThrowsDataValidationException()
    {
        $enum = ['foo', 'bar'];

        $data = 'test';

        $typeValidator = new StringValidator;

        $typeValidator->validateEnum($data, $enum);
    }

    /**
     * @test
     * @expectedException \Mooti\Validator\Exception\InvalidRuleException
     * @expectedExceptionMessage The regex property of This value needs to be set
     */
    public function validateRegexThrowsInvalidRuleException()
    {
        $constraints = [
            'regex' => ''
        ];

        $data = 'foobar';

        $typeValidator = new StringValidator;

        $typeValidator->validate($constraints, $data);
    }

    /**
     * @test
     * @expectedException \Mooti\Validator\Exception\DataValidationException
     * @expectedExceptionMessage bar is not an allowed value for This value. It does not match the regex: /foo/
     */
    public function validateRegexThrowsDataValidationException()
    {
        $regex = '/foo/';

        $data = 'bar';

        $typeValidator = new StringValidator;

        $typeValidator->validateRegex($data, $regex);
    }

    /**
     * @test
     * @expectedException \Mooti\Validator\Exception\InvalidRuleException
     * @expectedExceptionMessage An error occured. The regex for This value may be invalid
     */
    public function validateRegexThrowsInvalidRuleExceptionForInvalidRegex()
    {
        $regex = '/foo';

        $data = 'bar';

        $typeValidator = new StringValidator;

        $typeValidator->validateRegex($data, $regex);
    }

    /**
     * @test
     */
    public function validateRegexSucceeds()
    {
        $regex = '/foo/';

        $data = 'foo';

        $typeValidator = new StringValidator;

        $typeValidator->validateRegex($data, $regex);
    }

    public function testValidateStringWithEmailConstraintSucceeds()
    {
        $stringValidator = new StringValidator();
        $stringValidator->validate(['email'], 'user@host.tld');
    }

    /**
     * @expectedException \Mooti\Validator\Exception\DataValidationException
     * @expectedExceptionMessage invalidEmail is not a valid email address.
     */
    public function testValidateEmailAddressThrowsInvalidRuleException()
    {
        $stringValidator = new StringValidator();
        $stringValidator->validate(['email' => true], 'invalidEmail');
    }

}
