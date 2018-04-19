<?php

namespace Mooti\Test\PHPUnit\Validator\Functional;

use Mooti\Validator\Validator;
use Mooti\Validator\Exception\DataValidationException;

class ValidatorInheritenceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @dataProvider dataToValidate
     */
    public function validate($data, $valid, $errors)
    {
        $validationRules = [
            'animal' => [
                'name'        => 'Animal',
                'required'    => true,
                'type'        => 'object',
                'properties'  => [
                    'type' => [
                        'required' => true,
                        'type'     => 'string',
                        'constraints' => [
                            'enum' => ['cat', 'dog', 'mouse']
                        ]
                    ],
                    'age'  => [
                        'required' => true,
                        'type'     => 'number',
                        'constraints' => [
                            'integer' => true
                        ]
                    ]
                ],
                'inheritance' => [
                    'discriminator' => 'type',
                    'properties' => [
                        'cat' => [
                            'miceCaught' => [
                                'required' => true,
                                'type'     => 'number',
                                'constraints' => [
                                    'integer' => true
                                ]
                            ]
                        ],
                        'dog' => [
                            'carsChased' => [
                                'required' => true,
                                'type'     => 'number',
                                'constraints' => [
                                    'integer' => true
                                ]
                            ],
                            'collar' => [
                                'required' => true,
                                'type'     => 'object',
                                'properties' => [
                                    'colour' => [
                                        'required' => true,
                                        'type' => 'string'
                                    ]
                                ]
                            ]
                        ],
                        'mouse' => []
                    ]
                ]
            ]
        ];

        $validator = new Validator;
        $this->assertEquals($valid, $validator->isValid($validationRules, $data));
        $this->assertEquals($errors, $validator->getErrors());
    }

    public function dataToValidate()
    {
        return [
            [[], false, ['animal' => ['Animal is required']]],
            [['animal' => []], false, [
                'animal.type' => ['This value is required'],
                'animal.age' => ['This value is required']
            ]],
            [['animal' => [
                'type' => 'aardvark',
                'age'  => 2
            ]], false, [
                'animal.type' => [
                    'aardvark is not an allowed value for This value. Allowed values are: cat, dog, mouse'
                ]
            ]],
            [['animal' => [
                'type' => 'cat',
                'age'  => 2
            ]], false, [
                'animal.miceCaught' => [
                    'This value is required'
                ]
            ]],
            [['animal' => [
                'type' => 'cat',
                'age'  => 2,
                'miceCaught' => 3
            ]], true, []],
            [['animal' => [
                'type' => 'dog',
                'age'  => 3
            ]], false, [
                'animal.carsChased' => [
                    'This value is required'
                ],
                'animal.collar'     => [
                    'This value is required'
                ]
            ]],
            [['animal' => [
                'type' => 'dog',
                'age'  => 3,
                'carsChased' => 3,
                'collar' => []
            ]], false, [
                'animal.collar.colour'     => [
                    'This value is required'
                ]
            ]],
            [['animal' => [
                'type' => 'dog',
                'age'  => 3,
                'carsChased' => 3,
                'collar' => [
                    'colour' => 'red'
                ]
            ]], true, []],
            [['animal' => [
                'type' => 'mouse',
                'age'  => 1
            ]], true, []]
        ];
    }
}
