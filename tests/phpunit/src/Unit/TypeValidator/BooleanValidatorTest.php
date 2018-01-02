<?php

namespace Mooti\Test\PHPUnit\Validator\Unit\TypeValidator;

use Mooti\Validator\Exception\DataValidationException;
use Mooti\Validator\TypeValidator\BooleanValidator;

class BooleanValidatorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     * @expectedException \Mooti\Validator\Exception\DataValidationException
     * @expectedExceptionMessage This value must be boolean
     * @dataProvider badBooleans
     */
    public function validateDataThrowsDataValidationException($data)
    {
        $constraints = [];

        $typeValidator = new BooleanValidator();

        $typeValidator->validate($constraints, $data);
    }

    /**
     * @test
     * @dataProvider goodBooleans
     */
    public function validateDataSuceeds($data)
    {
        $constraints = [];

        $typeValidator = new BooleanValidator();

        $typeValidator->validate($constraints, $data);
    }

    public function badBooleans()
    {
        return [
            ['foobar'],
            [1],
            [''],
            [null]
        ];
    }

    public function goodBooleans()
    {
        return [
           [true],
           [false]
        ];
    }
}
