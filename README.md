# Mooti Validator

[![Build Status](https://travis-ci.org/mooti/validator-php.svg?branch=master)](https://travis-ci.org/mooti/validator-php)
[![Coverage Status](https://coveralls.io/repos/github/mooti/validator-php/badge.svg?branch=master)](https://coveralls.io/github/mooti/validator-php?branch=master)
[![Latest Stable Version](https://poser.pugx.org/mooti/validator-php/v/stable)](https://packagist.org/packages/mooti/validator-php)
[![Total Downloads](https://poser.pugx.org/mooti/validator-php/downloads)](https://packagist.org/packages/mooti/validator-php)
[![Latest Unstable Version](https://poser.pugx.org/mooti/validator-php/v/unstable)](https://packagist.org/packages/mooti/validator-php)
[![License](https://poser.pugx.org/mooti/validator-php/license)](https://packagist.org/packages/mooti/validator-php)

A standalone validator for json style data structures.

### Installation

You can install this through packagist

```
$ composer require mooti/validator-php
```

### Run the tests

If you would like to run the tests. Use the following:

```
$ ./bin/vendor/phpunit -c config/phpunit.xml
```

### Usage

The libray allows you to validate a json style data structure using a set of validation rules. The structure can be an array or a standard object (no other type of object will be validated). A validation rule is an associative array with a key corresponding to the item being validated. An example is:

```php
<?php
    require __DIR__.'/vendor/autoload.php';

    $rules = [
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

    //This will succeed
    $data = [
        'name' => 'Ken Lalobo',
        'age'  => 102,
        'address' => [
            'line1'    => 'test 1',
            'line2'    => 'test 2',
            'postCode' => 'BR12 2NN',
        ],
        'nickNames' => ['Len Kalobo', 'Kenny McKenface']
    ];

    $validator = new Mooti/Validator;

    if ($validator->isValid($rules, $data) == false) {
        print_r($validator->getErrors());
    }
```

