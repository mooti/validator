<?php

namespace Mooti\Test\PHPUnit\Validator;

use Mooti\Validator\Validator;

class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException Mooti\Validator\Exception\InvalidRuleException
     */
    public function validateEmptyNamedRuleThrowsValidationRuleException()
    {
        $validationRules = [
            'test' => []
        ];
        $data = [];

        $validator = new Validator;
        $validator->isValid($validationRules, $data);
    }

    /**
     * @test
     */
    public function validateEmptyWildcardRuleReturnsTrue()
    {
        $validationRules = [
            '*' => []
        ];
        $data = [];

        $validator = new Validator;
        self::assertTrue($validator->isValid($validationRules, $data));
    }

    /**
     * @test
     */
    public function validateRequiredNamedRuleEmptyDataReturnsFalse()
    {
        $validationRules = [
            'test' => [
                'required' => true
            ]
        ];
        $data = [];
        $errors = [
            'test' => ['This value is required']
        ];


        $validator = new Validator;
        self::assertFalse($validator->isValid($validationRules, $data));
        self::assertEquals($errors, $validator->getErrors());
    }

    /**
     * @test
     */
    public function validateRequiredWildcardRuleEmptyDataIsIgnoredAndReturnsTrue()
    {
        $validationRules = [
            '*' => [
                'required' => true
            ]
        ];
        $data = [];
        $errors = [];

        $validator = new Validator;
        self::assertTrue($validator->isValid($validationRules, $data));
        self::assertSame($errors, $validator->getErrors());
    }
}
