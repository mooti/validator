<?php

namespace Mooti\Test\PHPUnit\Validator\Unit;

use Mooti\Validator\Validator;
use Mooti\Validator\Exception\DataValidationException;
use Mooti\Validator\TypeValidator\TypeValidatorInterface;
use Mooti\Validator\TypeValidator\StringValidator;
use Mooti\Validator\TypeValidator\NumberValidator;
use Mooti\Validator\TypeValidator\ObjectValidator;
use Mooti\Validator\TypeValidator\ArrayValidator;

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
     * @expectedException \Mooti\Validator\Exception\InvalidRuleException
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
     * @expectedException \Mooti\Validator\Exception\InvalidRuleException
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
                [self::equalTo($validationRule), self::equalTo('bar1'), self::equalTo($data), self::equalTo('foo.bar1')],
                [self::equalTo($validationRule), self::equalTo('bar2'), self::equalTo($data), self::equalTo('foo.bar2')],
                [self::equalTo($validationRule), self::equalTo('bar3'), self::equalTo($data), self::equalTo('foo.bar3')]
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
                self::equalTo($data),
                self::equalTo('foo.bar')                
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
                self::equalTo($data),
                self::equalTo($fullyQualifiedName)
            );

        $validator->validateData($validationRule, $itemKey, $data, $fullyQualifiedName);
    }

    /**
     * @test
     * @expectedException \Mooti\Validator\Exception\InvalidRuleException
     * @expectedExceptionMessage hello does not have a "required". All named rules must have a "required" property
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
     * @expectedException \Mooti\Validator\Exception\DataValidationException
     * @expectedExceptionMessage This value is required
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

        $validator = $this->getMockBuilder(Validator::class)
            ->disableOriginalConstructor()
            ->setMethods(['propertyExists'])
            ->getMock();

        $validator->expects(self::once())
            ->method('propertyExists')
            ->with(
                self::equalTo($itemKey),
                self::equalTo($data)
            )
            ->will(self::returnValue(false));

        $validator->validateData($validationRule, $itemKey, $data, $fullyQualifiedName);
    }

    /**
     * @test     
     */
    public function validateDataReturnsIgnoresItem()
    {
        $itemKey = 'hello';
        $validationRule = [
            'type'     => 'string',
            'required' => false
        ];
        $fullyQualifiedName = 'hello';
        $data = []; 

        $validator = $this->getMockBuilder(Validator::class)
            ->disableOriginalConstructor()
            ->setMethods(['propertyExists'])
            ->getMock();

        $validator->expects(self::once())
            ->method('propertyExists')
            ->with(
                self::equalTo($itemKey),
                self::equalTo($data)
            )
            ->will(self::returnValue(false));

        $validator->validateData($validationRule, $itemKey, $data, $fullyQualifiedName);
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
            ->setMethods(['getProperty', 'validateItem', 'propertyExists'])
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

        $validator->expects(self::once())
            ->method('propertyExists')
            ->with(
                self::equalTo($itemKey),
                self::equalTo($data)
            )
            ->will(self::returnValue(true));

        $validator->validateData($validationRule, $itemKey, $data, $fullyQualifiedName);
    }

    /**
     * @test
     */
    public function validateItemReturnsTrue()
    {
        $itemKey = 'foo';
        $constraints = [
            'length' => [1, null]
        ];
        $validationRule = [
            'type' => 'string',
            'required' => false,
            'constraints' => $constraints
        ];
        $fullyQualifiedName = 'foo';
        $item = 'bar';

        $typeValidator = $this->getMockBuilder(TypeValidatorInterface::class)
            ->setMethods(['validate'])
            ->getMock();

        $typeValidator->expects(self::once())
            ->method('validate')
            ->with(
                self::equalTo($constraints),
                self::equalTo($item)
            );

        $validator = $this->getMockBuilder(Validator::class)
            ->disableOriginalConstructor()
            ->setMethods(['getTypeValidator'])
            ->getMock();

        $validator->expects(self::once())
            ->method('getTypeValidator')
            ->with(
                self::equalTo('string')
            )
            ->will(self::returnValue($typeValidator));


        $validator->validateItem($validationRule, $item, $fullyQualifiedName);
    }

    /**
     * @test
     */
    public function validateItemObjectReturnsTrue()
    {
        $itemKey = 'foo';
        $constraints = [];
        $properties = [
            'foo1' => [],
            'foo2' => []
        ];

        $validationRule = [
            'type'        => 'object',
            'required'    => false,
            'constraints' => $constraints,
            'properties'  => $properties
        ];
        $fullyQualifiedName = 'foo';
        $item = 'bar';

        $typeValidator = $this->getMockBuilder(TypeValidatorInterface::class)
            ->setMethods(['validate'])
            ->getMock();

        $typeValidator->expects(self::once())
            ->method('validate')
            ->with(
                self::equalTo($constraints),
                self::equalTo($item)
            );

        $validator = $this->getMockBuilder(Validator::class)
            ->disableOriginalConstructor()
            ->setMethods(['getTypeValidator', 'isValid'])
            ->getMock();

        $validator->expects(self::once())
            ->method('getTypeValidator')
            ->with(
                self::equalTo('object')
            )
            ->will(self::returnValue($typeValidator));

        $validator->expects(self::once())
            ->method('isValid')
            ->with(
                self::equalTo($properties),
                self::equalTo($item),
                self::equalTo($fullyQualifiedName)
            );

        $validator->validateItem($validationRule, $item, $fullyQualifiedName);
    }

    /**
     * @test
     */
    public function validateItemArrayReturnsTrue()
    {
        $itemKey = 'foo';
        $constraints = [];
        $items = [
            'foo1' => [],
            'foo2' => []
        ];

        $validationRule = [
            'type'        => 'array',
            'required'    => false,
            'constraints' => $constraints,
            'items'       => $items
        ];
        $fullyQualifiedName = 'foo';
        $item = 'bar';

        $typeValidator = $this->getMockBuilder(TypeValidatorInterface::class)
            ->setMethods(['validate'])
            ->getMock();

        $typeValidator->expects(self::once())
            ->method('validate')
            ->with(
                self::equalTo($constraints),
                self::equalTo($item)
            );

        $validator = $this->getMockBuilder(Validator::class)
            ->disableOriginalConstructor()
            ->setMethods(['getTypeValidator', 'isValid'])
            ->getMock();

        $validator->expects(self::once())
            ->method('getTypeValidator')
            ->with(
                self::equalTo('array')
            )
            ->will(self::returnValue($typeValidator));

        $validator->expects(self::once())
            ->method('isValid')
            ->with(
                self::equalTo($items),
                self::equalTo($item),
                self::equalTo($fullyQualifiedName)
            );

        $validator->validateItem($validationRule, $item, $fullyQualifiedName);
    }

    /**
     * @test
     */
    public function validateMultipleItemsSucceeds()
    {
        $validationRule = [
            'type' => 'string'
        ];
        $fullyQualifiedName = 'hello';
        $items = ['foo', 'bar']; 

        $rule1 = [
            'type' => 'string',
            'name' => 'Value number 1'
        ];
        $rule2 = [
            'type' => 'string',
            'name' => 'Value number 2'
        ];
        $validator = $this->getMockBuilder(Validator::class)
            ->disableOriginalConstructor()
            ->setMethods(['validateItem'])
            ->getMock();

        $validator->expects(self::exactly(2))
            ->method('validateItem')
            ->withConsecutive(
                [ self::equalTo($rule1), self::equalTo('foo'), self::equalTo($fullyQualifiedName)],
                [ self::equalTo($rule2), self::equalTo('bar'), self::equalTo($fullyQualifiedName)]
            );

        $validator->validateMultipleItems($validationRule, $items, $fullyQualifiedName);
    }

    /**
     * @test
     */
    public function propertyExistsArraySucceeds()
    {
        $property = 'foo';
        $data = ['foo' => 'bar'];

        $validator = new Validator;
        self::assertTrue($validator->propertyExists($property, $data));
    }

    /**
     * @test
     */
    public function propertyExistsObjectSucceeds()
    {
        $property = 'foo';
        $data = (object) ['foo' => 'bar'];

        $validator = new Validator;
        self::assertTrue($validator->propertyExists($property, $data));
    }

    /**
     * @test
     */
    public function propertyExistsOtherReturnsFalse()
    {
        $property = 'foo';
        $data = 'foobar';

        $validator = new Validator;
        self::assertFalse($validator->propertyExists($property, $data));
    }

    /**
     * @test
     */
    public function getPropertyArraySucceeds()
    {
        $property = 'foo';
        $data = ['foo' => 'bar'];

        $validator = new Validator;
        self::assertEquals('bar', $validator->getProperty($property, $data));
    }

    /**
     * @test
     */
    public function getPropertyObjectSucceeds()
    {
        $property = 'foo';
        $data = (object) ['foo' => 'bar'];

        $validator = new Validator;
        self::assertEquals('bar', $validator->getProperty($property, $data));
    }

    /**
     * @test
     */
    public function getPropertyOtherReturnsNull()
    {
        $property = 'foo';
        $data = 'foobar';

        $validator = new Validator;
        self::assertNull($validator->getProperty($property, $data));
    }

    /**
     * @test
     * @expectedException \Mooti\Validator\Exception\InvalidTypeValidatorException
     * @expectedExceptionMessage The type "foo" is invalid
     */
    public function getTypeValidatorThrowsInvalidTypeValidatorException()
    {
        $type = 'foo';
        $validator = new Validator;
        $validator->getTypeValidator($type);
    }

    /**
     * @test
     * @dataProvider validTypes
     */
    public function getTypeValidatorSucceeds($type, $className)
    {
        $typeValidator = $this->getMockBuilder(TypeValidatorInterface::class)
            ->setMethods(['validate'])
            ->getMock();

        $validator = $this->getMockBuilder(Validator::class)
            ->disableOriginalConstructor()
            ->setMethods(['createNew'])
            ->getMock();

        $validator->expects(self::once())
            ->method('createNew')
            ->with(
                self::equalTo($className)
            )
            ->will(self::returnValue($typeValidator));

        self::assertSame($typeValidator, $validator->getTypeValidator($type));
        self::assertSame($typeValidator, $validator->getTypeValidator($type));
    }

    public function validTypes()
    {
        return [
            ['string', StringValidator::class],
            ['number', NumberValidator::class],
            ['object', ObjectValidator::class],
            ['array',  ArrayValidator::class]
        ];
    }
}
