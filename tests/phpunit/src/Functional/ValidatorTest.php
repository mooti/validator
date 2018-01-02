<?php

namespace Mooti\Test\PHPUnit\Validator\Functional;

use Mooti\Validator\Validator;
use Mooti\Validator\Exception\DataValidationException;

class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function callBackTest($data)
    {
        if (str_word_count($data) != 2) {
            throw new DataValidationException('This value must have two words');            
        }
    }

    /**
     * @test
     * @dataProvider dataToValidate
     */
    public function validate($data, $valid, $errors)
    {
        $validationRules = [
            'title' => [
                'name'        => 'Title',
                'required'    => true,
                'nullable'    => true,
                'type'        => 'string',
                'constraints' => [
                    'length' => [1,null]
                ]
            ],
            'name' => [
                'name'        => 'Name',
                'required'    => true,
                'type'        => 'string',
                'constraints' => [
                    'length' => [1,null],
                    'callback' => [$this, 'callBackTest']
                ]
            ],
            'age' => [
                'name'        => 'Age',
                'required'    => false,
                'type'        => 'number',
                'constraints' => [
                    'integer' => true
                ]
            ],
            'address' => [
                'name'       => 'Address',
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
                'name'     => 'Nick Names',
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
            ],
            'languages' => [
                'name'     => 'Languages',
                'required' => false,
                'type'     => 'array',
                'items'    => [
                    '*' => [
                        'name' => 'Language',
                        'type' => 'string',
                        'constraints' => [
                            'length' => [1,null]
                        ]
                    ]
                ]
            ],
            'favouriteQuote' => [
                'name'     => 'Favourite Quote',
                'required' => false,
                'nullable' => true,
                'type'     => 'string',
                'constraints' => [
                    'length' => [1,null]
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
            [[], false, ['name' => ['Name is required'], 'title' => ['Title is required']]],
            [['title' => 1], false, ['name' => ['Name is required'], 'title' => ['Title must be a string']]],
            [['title' => null], false, ['name' => ['Name is required']]],
            [['title' => 'Mr'], false, ['name' => ['Name is required']]],
            [['title' => 'Mr', 'name' => null], false, ['name' => ['Name must be a string']]],
            [['title' => 'Mr', 'name' => 1], false, ['name' => ['Name must be a string']]],
            [['title' => 'Mr', 'name' => ''], false, ['name' => ['Name must have a length of at least 1']]],
            [['title' => 'Mr', 'name' => 'Ken'], false, ['name' => ['This value must have two words']]],
            [['title' => 'Mr', 'name' => 'Ken Lalobo'], true, []],
            [['title' => 'Mr', 'name' => 'Ken Lalobo', 'age' => 'test'], false, ['age' => ['Age must be a number']]],
            [['title' => 'Mr', 'name' => 'Ken Lalobo', 'age' => 0.1], false, ['age' => ['Age must  be an integer']]],
            [['title' => 'Mr', 'name' => 'Ken Lalobo', 'age' => 102], true, []],
            [
                ['title' => 'Mr', 'name' => 'Ken Lalobo', 'age' => 102, 'address' => null],
                false,
                ['address' => ['Address must be a standard object or an associative array']]
            ],
            [
                ['title' => 'Mr', 'name' => 'Ken Lalobo', 'age' => 102, 'address' => []],
                false,
                [
                    'address.line1'   => ['This value is required'],
                    'address.postCode' => ['This value is required']
                ]
            ],
            [
                [
                    'title' => 'Mr',
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
                    'title' => 'Mr',
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
                    'title' => 'Mr',
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
                    'title' => 'Mr',
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
                    'title' => 'Mr',
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
                    'title' => 'Mr',
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
                    'nickNames.*' => ['Value number 1 must be a string']
                ]
            ],
            [
                [
                    'title' => 'Mr',
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
                    'nickNames.*' => ['Value number 2 must be a string']
                ]
            ],
            [
                [
                    'title' => 'Mr',
                    'name' => 'Ken Lalobo',
                    'age' => 102,
                    'address' => [
                        'line1'    => 'test 1',
                        'line2'    => 'test 2',
                        'postCode' => 'BR12 2NN',
                    ],
                    'nickNames' => ['Len Kalobo', 'Lobs'],
                    'languages' => [1]
                ],
                false,
                [
                    'languages.*' => ['Language number 1 must be a string']
                ]
            ],
            [
                [
                    'title' => 'Mr',
                    'name' => 'Ken Lalobo',
                    'age' => 102,
                    'address' => [
                        'line1'    => 'test 1',
                        'line2'    => 'test 2',
                        'postCode' => 'BR12 2NN',
                    ],
                    'nickNames' => ['Len Kalobo', 'Lobs'],
                    'languages' => ['PHP', 1]
                ],
                false,
                [
                    'languages.*' => ['Language number 2 must be a string']
                ]
            ],
            [
                [
                    'title' => 'Mr',
                    'name' => 'Ken Lalobo',
                    'age' => 102,
                    'address' => [
                        'line1'    => 'test 1',
                        'line2'    => 'test 2',
                        'postCode' => 'BR12 2NN',
                    ],
                    'nickNames' => ['Len Kalobo', 'Lobs'],
                    'languages' => ['PHP', 'Javascript'],
                    'favouriteQuote' => 1
                ],
                false,
                [
                    'favouriteQuote' => [
                        'Favourite Quote must be a string'
                    ]
                ]
            ],
            [
                [
                    'title' => 'Mr',
                    'name' => 'Ken Lalobo',
                    'age' => 102,
                    'address' => [
                        'line1'    => 'test 1',
                        'line2'    => 'test 2',
                        'postCode' => 'BR12 2NN',
                    ],
                    'nickNames' => ['Len Kalobo', 'Lobs'],
                    'languages' => ['PHP', 'Javascript'],
                    'favouriteQuote' => ''
                ],
                false,
                [
                    'favouriteQuote' => [
                        'Favourite Quote must have a length of at least 1'
                    ]
                ]
            ],
            [
                [
                    'title' => 'Mr',
                    'name' => 'Ken Lalobo',
                    'age' => 102,
                    'address' => [
                        'line1'    => 'test 1',
                        'line2'    => 'test 2',
                        'postCode' => 'BR12 2NN',
                    ],
                    'nickNames' => ['Len Kalobo', 'Lobs'],
                    'languages' => ['PHP', 'Javascript'],
                    'favouriteQuote' => null
                ],
                true,
                []
            ],
            [
                [
                    'title' => 'Mr',
                    'name' => 'Ken Lalobo',
                    'age' => 102,
                    'address' => [
                        'line1'    => 'test 1',
                        'line2'    => 'test 2',
                        'postCode' => 'BR12 2NN',
                    ],
                    'nickNames' => ['Len Kalobo', 'Lobs'],
                    'languages' => ['PHP', 'Javascript'],
                    'favouriteQuote' => 'Seize the day... Tomorrow'
                ],
                true,
                []
            ]

        ];
    }
}
