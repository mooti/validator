<?php
/**
 * Validator
 *
 * Validator class
 *
 * @package      Mooti
 * @subpackage   Validator
 * @author       Ken Lalobo <ken@mooti.io>
 */

namespace Mooti\Validator;

use Mooti\Xizlr\Testable\Testable;
use Mooti\Validator\Exception\InvalidRuleException;
use Mooti\Validator\Exception\DataValidationException;

class Validator
{
    use Testable;

    const TYPE_STRING = 'string';
    const TYPE_NUMBER = 'number';
    const TYPE_OBJECT = 'object';
    const TYPE_ARRAY  = 'array';

    protected $allowedTypeValidators = [
        self::TYPE_STRING,
        self::TYPE_NUMBER,
        self::TYPE_OBJECT,
        self::TYPE_ARRAY
    ];

    protected $errors = [];
    protected $typeValidators = [];

    /**
     * Validate the data
     *
     * @param array $validationRules The validaton rules
     * @param array $data The data to validate
     *
     * @return boolean Whether it was valid or not
     */
    public function isValid(array $validationRules, array $data, $nameSpacePrefix = '')
    {
        foreach ($validationRules as $validationKey => $validationRule) {
            // All named rules need to let us know if they are required or not
            if ($validationKey != '*' && isset($validationRule['required']) == false) {
                throw new InvalidRuleException('A named rule must have a "required" property');
            }

            // There can only be one rule if using wildcards
            if ($validationKey == '*' && count($validationRules) > 1) {
                throw new InvalidRuleException('There can only be one rul if using wildcards');
            }

            $fullKeyName = $nameSpacePrefix.$validationKey;

            if ($validationKey != '*' && $validationRule['required'] == true && $this->propertyExists($validationKey, $data) == false) {
                if (!isset($this->errors[$fullKeyName])) {
                    $this->errors[$fullKeyName] = [];
                }
                $this->errors[$fullKeyName][] = 'This value is required';
                //The item does not exist but is required so no need to validate. make a note and stop validation on it
                continue;
            } elseif ($validationKey != '*' && $validationRule['required'] == false && $this->propertyExists($validationKey, $data) == false) {
                //The item does not exist and is not required so no need to validate
                continue;
            }

            $validationType = $validationRule['type'];
            $typeValidator = $this->getTypeValidator($validationType);
            try {
                $parameters = isset($validationRule['constraints']) ? $validationRule['constraints'] : []; 

                if ($validationKey != '*') {
                    $typeValidator->validate($parameters, $this->getProperty($validationKey, $data));
                } else {
                    for ($i = 0; $i < sizeof($data); $i++) {
                        $typeValidator->validate($parameters, $data[$i]);
                    }
                }               
                
            } catch (DataValidationException $e) {
                if (!isset($this->errors[$fullKeyName])) {
                    $this->errors[$fullKeyName] = [];
                }
                $this->errors[$fullKeyName][] = $e->getMessage();
                continue;
            }
            if ($validationType == 'object' && isset($validationRule['properties']) && is_array($validationRule['properties'])) {
                $this->isValid($validationRule['properties'], $this->getProperty($validationKey, $data), $fullKeyName . '.');
            } elseif ($validationType == 'array' && isset($validationRule['items']) && is_array($validationRule['items'])) {
                $this->isValid($validationRule['items'], $this->getProperty($validationKey, $data), $fullKeyName . '.');
            }
        };

        if (sizeof($this->errors) > 0) {
            return false;
        } else {
            return true;
        }
    }

    public function propertyExists($property, $data)
    {
        if (gettype($data) == 'array') {
            return array_key_exists($property, $data);
        } elseif (gettype($data) == 'object') {
            return property_exists($data , $property);
        }
        return false;
    }

    public function getProperty($property, $data)
    {
        if (gettype($data) == 'array') {
            return $data[$property];
        } elseif (gettype($data) == 'object') {
            return $data->$property;
        }
        return null;
    }

    /**
     * Get any validation errors generated
     *
     * @return TypeValidatorInterface The type validator
     */
    public function getTypeValidator($type)
    {
        if (in_array($type, $this->allowedTypeValidators, true) == false) {
            throw new InvalidTypeValidatorException('The type "'.$type.'"" is invalid');
        }

        if (isset($this->typeValidators[$type]) == false) {
            $className = 'Mooti\\Validator\\TypeValidator\\'.ucfirst($type).'Validator';
            $this->typeValidators[$type] = $this->createNew($className);
        }

        return $this->typeValidators[$type];
    }

    /**
     * Get type validators
     *
     * @return array An array of errors
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
