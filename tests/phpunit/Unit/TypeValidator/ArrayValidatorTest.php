<?php

namespace Mooti\Test\PHPUnit\Validator\Unit\TypeValidator;

use Mooti\Validator\Exception\DataValidationException;
use Mooti\Validator\TypeValidator\ArrayValidator;

class ArrayValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function validateSucceeds()
    {
        $constraints = [];

        $data = ['hello1', 'hello2'];

        $typeValidator = $this->getMockBuilder(ArrayValidator::class)
            ->disableOriginalConstructor()
            ->setMethods(['validateSequentialArray'])
            ->getMock();

        $typeValidator->expects(self::once(3))
            ->method('validateSequentialArray')
            ->with(self::equalTo($data))
            ->will(self::returnValue(true));

        $typeValidator->validate($constraints, $data);
    }

    /**
     * @test
     * @expectedException Mooti\Validator\Exception\DataValidationException
     * @expectedExceptionMessage This value must be a sequential array
     */
    public function validateDataThrowsDataValidationException()
    {
        $constraints = [];

        $data ='foobar';

        $typeValidator = new ArrayValidator;

        $typeValidator->validate($constraints, $data);
    }

    /**
     * @test
     * @expectedException Mooti\Validator\Exception\DataValidationException
     * @expectedExceptionMessage This value is an array but it is not sequential
     */
    public function validateSequentialArrayThrowsDataValidationException()
    {
        $data = ['foo' => 'bar'];

        $typeValidator = new ArrayValidator;

        $typeValidator->validateSequentialArray($data);
    }

    /**
     * @test
     */
    public function validateSequentialArraySucceeds()
    {
        $data = ['foo', 'bar'];

        $typeValidator = new ArrayValidator;

        $typeValidator->validateSequentialArray($data);
    }
}
