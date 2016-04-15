<?php

namespace Mooti\Test\PHPUnit\Validator\Unit;

use Mooti\Validator\Validator;

class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function addHasAndGetErrorsWithNoErrorsSucceed()
    {
        $errors = [];

        $validator = new Validator;

        $this->assertFalse($validator->hasErrors());
        $this->assertEquals($errors, $validator->getErrors());
    }

    /**
     * @test
     */
    public function addHasAndGetErrorsWithErrorsSucceed()
    {
        $errors = [
            'test.1' => [
                'this is an error',
                'this is another error'
            ],
            'test.2' => [
                'this is a third error'
            ]
        ];

        $validator = new Validator;
        $validator->addError('test.1', 'this is an error');
        $validator->addError('test.1', 'this is another error');
        $validator->addError('test.2', 'this is a third error');

        $this->assertTrue($validator->hasErrors());
        $this->assertEquals($errors, $validator->getErrors());
    }

    /**
     * @test
     * @expectedException Mooti\Validator\Exception\InvalidRuleException
     * @expectedExceptionMessage All rules must have a "type" property
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
     * @expectedException Mooti\Validator\Exception\InvalidRuleException
     * @expectedExceptionMessage You cannot have more than one rule if using a wildcard
     */
    public function validateMultipleRulesWithWildCardThrowsValidationRuleException()
    {
        $validationRule = [
            'type' => 'string'
        ];

        $validationRules = [
            '*' => $validationRule
        ];

        $data = [
            'hello', 'whoohoo'
        ];

        $validator = $this->getMockBuilder(Validator::class)
            ->disableOriginalConstructor()
            ->setMethods('validateMultipleItems')
            ->getMock();

        $validator->expects(self::once())
            ->method('validateMultipleItems')
            ->with(
                self::equalTo($validationRule),
                self::equalTo($data)
            )
            ->will(self::returnValue(false));

        $validator->isValid($validationRules, $data);
    }

    /**
     * @test
     */
    public function isValidWithIgnoredWildCardReturnsFalse()
    {
        $validator = new Validator;
        $validator->addError('test.1', 'this is an error');
        $validator->addError('test.1', 'this is another error');
        $validator->addError('test.2', 'this is a third error');

        $this->assertTrue($validator->hasErrors());
        $this->assertEquals($errors, $validator->getErrors());
    }

}
