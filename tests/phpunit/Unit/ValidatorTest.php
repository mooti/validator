<?php

namespace Mooti\Test\PHPUnit\Validator\Unit;

use Mooti\Validator\Validator;
use Mooti\Validator\Exception\DataValidationException;
use Mooti\Validator\TypeValidator\TypeValidatorInterface;

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

        $validationRules = [
            '*' => ['type' => 'string'],
            'abother_rule' => ['type' => 'string']
        ];

        $data = [];

        $validator = new Validator;
        $validator->isValid($validationRules, $data);
    }

    /**
     * @test
     */
    public function isValidReturnsTrue()
    {
        $validationRule = [
            'type' => 'string'
        ];

        $validationRules = [
            'bar1' => $validationRule,
            'bar2' => $validationRule,
            'bar3' => $validationRule
        ];

        $data = [
            'bar1' => 'hello 1',
            'bar2' => 'hello 2',
            'bar3' => 'hello 3'
        ];

        $validator = $this->getMockBuilder(Validator::class)
            ->disableOriginalConstructor()
            ->setMethods(['validateData', 'hasErrors'])
            ->getMock();

        $validator->expects(self::exactly(3))
            ->method('validateData')
            ->withConsecutive(
                [self::equalTo($validationRule), self::equalTo('bar1'), self::equalTo('foo.bar1'), self::equalTo($data)],
                [self::equalTo($validationRule), self::equalTo('bar2'), self::equalTo('foo.bar2'), self::equalTo($data)],
                [self::equalTo($validationRule), self::equalTo('bar3'), self::equalTo('foo.bar3'), self::equalTo($data)]
            );

        $validator->expects(self::once())
            ->method('hasErrors')
            ->will(self::returnValue(false));

        self::assertTrue($validator->isValid($validationRules, $data, 'foo'));
    }

    /**
     * @test
     */
    public function isValidReturnsFalse()
    {
        $validationRule = [
            'type' => 'string'
        ];

        $validationRules = [
            'bar' => $validationRule
        ];

        $data = [
            'bar' => 'hello'
        ];

        $fullyQualifiedName = 'foo.bar';

        $validator = $this->getMockBuilder(Validator::class)
            ->disableOriginalConstructor()
            ->setMethods(['validateData', 'addError', 'hasErrors'])
            ->getMock();

        $validator->expects(self::once())
            ->method('validateData')
            ->with(
                self::equalTo($validationRule),
                self::equalTo('bar'),
                self::equalTo('foo.bar'),
                self::equalTo($data)                
            )
            ->will(self::throwException(new DataValidationException('this is an error message')));

        $validator->expects(self::once())
            ->method('addError')
            ->with(
                self::equalTo($fullyQualifiedName),
                self::equalTo('this is an error message')
            );

        $validator->expects(self::once())
            ->method('hasErrors')
            ->will(self::returnValue(true));

        self::assertFalse($validator->isValid($validationRules, $data, 'foo'));
    }

    /**
     * @test
     */
    public function validateDataValidatesMultipleItems()
    {
        $itemKey = '*';
        $validationRule = ['type' => 'string'];
        $fullyQualifiedName = '*';
        $data = []; 

        $validator = $this->getMockBuilder(Validator::class)
            ->disableOriginalConstructor()
            ->setMethods(['validateMultipleItems'])
            ->getMock();

        $validator->expects(self::once())
            ->method('validateMultipleItems')
            ->with(
                self::equalTo($validationRule),
                self::equalTo($fullyQualifiedName),
                self::equalTo($data)
            );

        self::assertTrue($validator->validateData($validationRule, $itemKey, $fullyQualifiedName, $data));
    }

    /**
     * @test
     * @expectedException Mooti\Validator\Exception\InvalidRuleException
     * @expectedExceptionMessage A named rule must have a "required" property
     */
    public function validateDataThrowsInvalidRuleException()
    {
        $itemKey = 'hello';
        $validationRule = ['type' => 'string'];
        $fullyQualifiedName = 'hello';
        $data = []; 

        $validator = new Validator;

        $validator->validateData($validationRule, $itemKey, $fullyQualifiedName, $data);
    }

    /**
     * @test
     * @expectedException Mooti\Validator\Exception\DataValidationException
     * @DataValidationException This value is required
     */
    public function validateDataThrowsDataValidationException()
    {
        $itemKey = 'hello';
        $validationRule = [
            'type'     => 'string',
            'required' => true
        ];
        $fullyQualifiedName = 'hello';
        $data = []; 

        $validator = new Validator;

        $validator->validateData($validationRule, $itemKey, $fullyQualifiedName, $data);
    }

    /**
     * @test     
     */
    public function validateDataReturnsFalse()
    {
        $itemKey = 'hello';
        $validationRule = [
            'type'     => 'string',
            'required' => false
        ];
        $fullyQualifiedName = 'hello';
        $data = []; 

        $validator = new Validator;

        self::assertFalse($validator->validateData($validationRule, $itemKey, $fullyQualifiedName, $data));
    }

    /**
     * @test
     */
    public function validateDataValidatesItem()
    {
        $itemKey = 'foo';
        $validationRule = [
            'type' => 'string',
            'required' => false
        ];
        $fullyQualifiedName = 'foo';
        $item = 'bar';
        $data = [
            'foo' => $item
        ]; 

        $validator = $this->getMockBuilder(Validator::class)
            ->disableOriginalConstructor()
            ->setMethods(['getProperty', 'validateItem'])
            ->getMock();

        $validator->expects(self::once())
            ->method('getProperty')
            ->with(
                self::equalTo($itemKey),
                self::equalTo($data)
            )
            ->will(self::returnValue($item));

        $validator->expects(self::once())
            ->method('validateItem')
            ->with(
                self::equalTo($validationRule),
                self::equalTo($item),
                self::equalTo($fullyQualifiedName)
            );

        self::assertTrue($validator->validateData($validationRule, $itemKey, $fullyQualifiedName, $data));
    }

    /**
     * @test
     */
    public function validateItemReturnsTrue()
    {
        $this->markTestIncomplete();

        $itemKey = 'foo';
        $validationRule = [
            'type' => 'string',
            'required' => false
        ];
        $fullyQualifiedName = 'foo';
        $item = 'bar';

        $typeValidator = $this->getMockBuilder(TypeValidatorInterface::class)
            ->setMethods(['validate'])
            ->getMock();

        $validator->expects(self::once())
            ->method('validateItem')
            ->with(
                self::equalTo($validationRule),
                self::equalTo($item),
                self::equalTo($fullyQualifiedName)
            );

        $validator = $this->getMockBuilder(Validator::class)
            ->disableOriginalConstructor()
            ->setMethods(['getTypeValidator'])
            ->getMock();

        $validator->expects(self::once())
            ->method('getTypeValidator')
            ->with(
                self::equalTo($itemKey),
                self::equalTo($data)
            )
            ->will(self::returnValue($item));


        self::assertTrue($validator->validateItem($validationRule, $item, $fullyQualifiedName));
    }

}
