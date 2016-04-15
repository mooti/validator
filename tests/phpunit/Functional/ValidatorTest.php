<?php

namespace Mooti\Test\PHPUnit\Validator\Functional;

use Mooti\Validator\Validator;

class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @dataProvider dataToValidate
     */
    public function validate($data, $valid, $errors)
    {
        $validationRules = [
            'name' => [
                'required'    => true,
                'type'        => 'string',
                'constraints' => [
                    'length' => [1,null]
                ]
            ],
            'age' => [
                'required'    => false,
                'type'        => 'number',
                'constraints' => [
                    'integer' => true
                ]
            ],
            'address' => [
                'required'   => false,
                'type'       => 'object',
                'properties' => [
                    'line1' => [
                        'required' => true,
                        'type'     => 'string',
                        'constraints' => [
                            'length' => [1,null]
                        ]
                    ],
                    'line2' => [
                        'required' => false,
                        'type'     => 'string'
                    ],
                    'postCode' => [
                        'required' => true,
                        'type'     => 'string',
                        'constraints' => [
                            'length' => [3,12]
                        ]
                    ]
                ]
            ],
            'nickNames' => [
                'required' => false,
                'type'     => 'array',
                'items'    => [
                    '*' => [
                        'type' => 'string',
                        'constraints' => [
                            'length' => [1,null]
                        ]
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
            [[], false, ['name' => ['This value is required']]],
            [['name' => null], false, ['name' => ['This value must be a string']]],
            [['name' => 1], false, ['name' => ['This value must be a string']]],
            [['name' => ''], false, ['name' => ['This value must have a length of at least 1']]],
            [['name' => 'Ken Lalobo'], true, []],
            [['name' => 'Ken Lalobo', 'age' => 'test'], false, ['age' => ['This value must be a number']]],
            [['name' => 'Ken Lalobo', 'age' => 0.1], false, ['age' => ['This value must  be an integer']]],
            [['name' => 'Ken Lalobo', 'age' => 102], true, []],
            [
                ['name' => 'Ken Lalobo', 'age' => 102, 'address' => null],
                false,
                ['address' => ['This value must be a standard object or an associative array']]
            ],
            [
                ['name' => 'Ken Lalobo', 'age' => 102, 'address' => []],
                false,
                [
                    'address.line1'   => ['This value is required'],
                    'address.postCode' => ['This value is required']
                ]
            ],
            [
                [
                    'name' => 'Ken Lalobo',
                    'age' => 102,
                    'address' => [
                        'line1'    => 'test',
                        'postCode' => 'This is a really long postcode',
                    ]
                ],
                false,
                [
                    'address.postCode' => ['This value must have a length less than or equal to 12']
                ]
            ],
            [
                [
                    'name' => 'Ken Lalobo',
                    'age' => 102,
                    'address' => [
                        'line1'    => 'test',
                        'postCode' => 'BR12 2NN',
                    ]
                ],
                true,
                []
            ],
            [
                [
                    'name' => 'Ken Lalobo',
                    'age' => 102,
                    'address' => [
                        'line1'    => 'test',
                        'line2'    => 1,
                        'postCode' => 'BR12 2NN',
                    ]
                ],
                false,
                [
                    'address.line2' => ['This value must be a string']
                ]
            ],
            [
                [
                    'name' => 'Ken Lalobo',
                    'age' => 102,
                    'address' => [
                        'line1'    => 'test 1',
                        'line2'    => 'test 2',
                        'postCode' => 'BR12 2NN',
                    ]
                ],
                true,
                []
            ],
            [
                [
                    'name' => 'Ken Lalobo',
                    'age' => 102,
                    'address' => [
                        'line1'    => 'test 1',
                        'line2'    => 'test 2',
                        'postCode' => 'BR12 2NN',
                    ],
                    'nickNames' => []
                ],
                true,
                []
            ],
            [
                [
                    'name' => 'Ken Lalobo',
                    'age' => 102,
                    'address' => [
                        'line1'    => 'test 1',
                        'line2'    => 'test 2',
                        'postCode' => 'BR12 2NN',
                    ],
                    'nickNames' => [1]
                ],
                false,
                [
                    'nickNames.*' => ['Item[0] : This value must be a string']
                ]
            ],
            [
                [
                    'name' => 'Ken Lalobo',
                    'age' => 102,
                    'address' => [
                        'line1'    => 'test 1',
                        'line2'    => 'test 2',
                        'postCode' => 'BR12 2NN',
                    ],
                    'nickNames' => ['Len Kalobo', 1]
                ],
                false,
                [
                    'nickNames.*' => ['Item[1] : This value must be a string']
                ]
            ],
            [
                [
                    'name' => 'Ken Lalobo',
                    'age' => 102,
                    'address' => [
                        'line1'    => 'test 1',
                        'line2'    => 'test 2',
                        'postCode' => 'BR12 2NN',
                    ],
                    'nickNames' => ['Len Kalobo', 'Lobs']
                ],
                true,
                []
            ],

        ];
    }
}
