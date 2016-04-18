<?php

namespace Mooti\Test\PHPUnit\Validator\Unit\TypeValidator;

use Mooti\Validator\Exception\DataValidationException;
use Mooti\Validator\TypeValidator\ObjectValidator;

class ObjectValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function validateWithObjectSucceeds()
    {
        $constraints = [];

        $data = (object) ['hello1' => 'foo', 'hello2' => 'bar'];

        $typeValidator = $this->getMockBuilder(ObjectValidator::class)
            ->disableOriginalConstructor()
            ->setMethods(['validateObject'])
            ->getMock();

        $typeValidator->expects(self::once())
            ->method('validateObject')
            ->with(self::equalTo($data));

        $typeValidator->validate($constraints, $data);
    }

    /**
     * @test
     */
    public function validateWithArraySucceeds()
    {
        $constraints = [];

        $data = ['hello1' => 'foo', 'hello2' => 'bar'];

        $typeValidator = $this->getMockBuilder(ObjectValidator::class)
            ->disableOriginalConstructor()
            ->setMethods(['validateAssociativeArray'])
            ->getMock();

        $typeValidator->expects(self::once())
            ->method('validateAssociativeArray')
            ->with(self::equalTo($data));

        $typeValidator->validate($constraints, $data);
    }

    /**
     * @test
     * @expectedException Mooti\Validator\Exception\DataValidationException
     * @expectedExceptionMessage This value must be a standard object or an associative array
     */
    public function validateThrowsDataValidationException()
    {
        $constraints = [];

        $data ='foobar';

        $typeValidator = new ObjectValidator;

        $typeValidator->validate($constraints, $data);
    }

    /**
     * @test
     * @expectedException Mooti\Validator\Exception\DataValidationException
     * @expectedExceptionMessage This value must be an instance of the stdClass
     */
    public function validateObjectThrowsDataValidationException()
    {
        $data = new \DateTime;

        $typeValidator = new ObjectValidator;

        $typeValidator->validateObject($data);
    }

    /**
     * @test
     */
    public function validateObjectSucceeds()
    {
        $data = (object) ['hello1' => 'foo', 'hello2' => 'bar'];

        $typeValidator = new ObjectValidator;

        $typeValidator->validateObject($data);
    }

    /**
     * @test
     * @expectedException Mooti\Validator\Exception\DataValidationException
     * @expectedExceptionMessage This value is an array but it is not associative
     */
    public function validateAssociativeArrayThrowsDataValidationException()
    {
        $data = ['foo', 'bar'];

        $typeValidator = new ObjectValidator;

        $typeValidator->validateAssociativeArray($data);
    }

    /**
     * @test
     */
    public function validateAssociativeArraySucceeds()
    {
        $data = ['hello1' => 'foo', 'hello2' => 'bar'];

        $typeValidator = new ObjectValidator;

        $typeValidator->validateAssociativeArray($data);
    }
}
