# Mooti Validator

[![Build Status](https://travis-ci.org/mooti/validator.svg?branch=master)](https://travis-ci.org/mooti/validator)
[![Coverage Status](https://coveralls.io/repos/github/mooti/validator/badge.svg?branch=master)](https://coveralls.io/github/mooti/validator?branch=master)
[![Latest Stable Version](https://poser.pugx.org/mooti/validator/v/stable)](https://packagist.org/packages/mooti/validator)
[![Total Downloads](https://poser.pugx.org/mooti/validator/downloads)](https://packagist.org/packages/mooti/validator)
[![Latest Unstable Version](https://poser.pugx.org/mooti/validator/v/unstable)](https://packagist.org/packages/mooti/validator)
[![License](https://poser.pugx.org/mooti/validator/license)](https://packagist.org/packages/mooti/validator)

A standalone validator for json style data structures.

### Installation

You can install this through packagist

```
$ composer require mooti/validator
```

### Run the tests

If you would like to run the tests. Use the following:

```
$ ./vendor/bin/phpunit -c config/phpunit.xml
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

#### Rules

The rules follow a simple structure. Each rule has a key that corresponds to the key in your data structure.

```
$rules = [
    'name' => [
        'type' => 'string'
    ]
];
```

For numeric arrays, the key is an asterisk `*` and they are called wildcard rules. Wildcard rules will validate against every item in the data structure (excluding children). If you use a wildcard rule, you cannot add additional rules for that level of validation.

```
$rules = [
    '*' => [
        'type' => 'string'
    ]
];
```

All rules have a `type` property. Each type has additional properties.

- **string**
   ```
    $rules = [
        'name' => [
            'required'    => true,
            'type'        => 'string',
            'constraints' => [
                'length' => [1,null]
            ]
        ]
    ]
   ```
   The string type validates the item as a string. it also has the following properties:
   * **required** [*true/false*] : wether the item is required
   * **constraints** [*array*] : an optional associative array of constraints. These are:
      * **length** [*array*] : the minimum and maximum length of the string as a numeric array in the format [min, max]. If you don't want to set a value set it to null. So [1,null] will be a string with a minimum of one character but no maximum set.

- **number**

   ```
   $rules = [
       'required'    => false,
            'type'        => 'number',
            'constraints' => [
                'integer' => true
            ]
        ]
    ]
   ```
   The number type validates the item as a number. it also has the following properties:
   * **required** [*true/false*] : wether the item is required
   * **constraints** [*array*] : an optional associative array of constraints. These are:
      * **integer** [*true/false*] : Wether this has to be an integer. true validates it to be an integer, false validates it to be anything but an integer

- **object**

   ```
   $rules = [
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
                ]
            ]
        ]
    ]
   ```
   The object type validates the item as an associative array/standard object. it also has the following properties:
   * **required** [*true/false*] : wether the item is required
   * **properties** [*array*] : an optional associative array of rules for the object's properties. You can use a wildcard rule here.

- **array**

   ```
   $rules = [
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
    ]
   ```
   The array type validates the item as an numeric array/standard object. it also has the following properties:
   * **required** [*true/false*] : wether the item is required
   * **items** [*array*] : an optional associative array of rules for the array's items. This should be a wildcard rule and nothing else.