<?php
/**
 * Validator
 *
 * Main validator class
 *
 * This is the main class for validation. It will validate a data structure
 * based on teh validation rules you give it.
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
     * Add an error to the internal error array
     *
     * @param string $errorKey   The key to use for the error
     * @param string $errorValue The description of the error
     *
     */
    public function addError($errorKey, $errorValue)
    {
        if (!isset($this->errors[$errorKey])) {
            $this->errors[$errorKey] = [];
        }
        $this->errors[$errorKey][] = $errorValue;
    }

    /**
     * Indicates wether we have errors or not     
     *
     * @return boolean Wether there are any erros
     */
    public function hasErrors()
    {
        return (sizeof($this->errors) > 0);
    }

    /**
     * Get all errors
     *
     * @return array An array of errors
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Validate the data
     *
     * @param array $validationRules The validaton rules
     * @param array $data The data to validate
     *
     * @return boolean Whether it was valid or not
     */
    public function isValid(array $validationRules, array $data, $nameSpace = '')
    {
        foreach ($validationRules as $itemKey => $validationRule) {

            // All rules need to have a type
            if (isset($validationRule['type']) == false) {
                throw new InvalidRuleException('All rules must have a "type" property');
            }

            // There can only be one rule if using wildcards
            if ($itemKey == '*' && count($validationRules) > 1) {
                throw new InvalidRuleException('You cannot have more than one rule if using a wildcard');
            }

            try {
                $fullyQualifiedName = $nameSpace . (empty($nameSpace) == false ?'.':'') .$itemKey;
                $this->validateData($validationRule, $itemKey, $data, $fullyQualifiedName);
            } catch (DataValidationException $e) {
                $this->addError($fullyQualifiedName, $e->getMessage());
            }
        };

        if ($this->hasErrors()) {
            return false;
        } else {
            return true;
        }
    }

    public function validateData(array $validationRule, $itemKey, $data, $fullyQualifiedName)
    {
        if ($itemKey == '*') {
            $this->validateMultipleItems($validationRule, $data);
        } else {
            // All named rules need to let us know if they are required or not
            if (isset($validationRule['required']) == false) {
                throw new InvalidRuleException('A named rule must have a "required" property');
            }

            if ($validationRule['required'] == true && $this->propertyExists($itemKey, $data) == false) {
                throw new DataValidationException('This value is required');
            } elseif ($validationRule['required'] == false && $this->propertyExists($itemKey, $data) == false) {
                //The item does not exist and is not required so no need to validate
                return false;
            }

            $item = $this->getProperty($itemKey, $data);
            $this->validateItem($validationRule, $item);
        }

        $validationType = $validationRule['type'];

        if ($validationType == 'object' && isset($validationRule['properties']) && is_array($validationRule['properties'])) {
            $this->isValid($validationRule['properties'], $this->getProperty($itemKey, $data), $fullyQualifiedName);
        } elseif ($validationType == 'array' && isset($validationRule['items']) && is_array($validationRule['items'])) {
            $this->isValid($validationRule['items'], $this->getProperty($itemKey, $data), $fullyQualifiedName);
        }

        return true;
    }

    public function validateItem(array $validationRule, $item)
    {
        $typeValidator = $this->getTypeValidator($validationRule['type']);
        
        $constraints = isset($validationRule['constraints']) ? $validationRule['constraints'] : []; 
        $typeValidator->validate($constraints, $item);

        return true;
    }

    public function validateMultipleItems(array $validationRule, $data)
    {
        $typeValidator = $this->getTypeValidator($validationRule['type']);
        
        $constraints = isset($validationRule['constraints']) ? $validationRule['constraints'] : []; 
        try {            
            for ($i = 0; $i < sizeof($data); $i++) {
                $typeValidator->validate($constraints, $data[$i]);
            }
        } catch (DataValidationException $e) {
            throw new DataValidationException('Item['.$i.'] : '.$e->getMessage());
        }

        return true;
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
}
