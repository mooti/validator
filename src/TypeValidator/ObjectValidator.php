<?php
/**
 * Object Validator
 *
 * Object Validator class
 *
 * @package      Mooti
 * @subpackage   Validator
 * @author       Ken Lalobo <ken@mooti.io>
 */

namespace Mooti\Validator\TypeValidator;

use Mooti\Factory\Factory;
use Mooti\Validator\Exception\DataValidationException;

class ObjectValidator implements TypeValidatorInterface
{
    use Factory;
    
    /**
     * Validate some data
     *
     * @param array $constraints The rules
     * @param mixed $data        The data to validate
     *
     * @return boolean Wether it was valid or not
     */
    public function validate(array $constraints, $data)
    {
        if (gettype($data) == 'object') {
            $this->validateObject($data);
        } elseif (gettype($data) == 'array') {
            $this->validateAssociativeArray($data);
        } else {
            throw new DataValidationException('This value must be a standard object or an associative array');
        }
    }

    /**
     * Validate that the data is an instance of the stdClass
     *
     * @param object $data The data to validate
     *
     * @throws DataValidationException
     */
    public function validateObject($data)
    {
        if (!$data instanceof \stdClass) {
            throw new DataValidationException('This value must be an instance of the stdClass');
        }
    }

    /**
     * Validate that the data is an associative array
     *
     * @param array $data The data to validate
     *
     * @throws DataValidationException
     */
    public function validateAssociativeArray(array $data)
    {
        //if the array has keys that are both numeric AND sequential then it is not associative
        if (!empty($data) && array_keys($data) === range(0, count($data) - 1)) {
            throw new DataValidationException('This value is an array but it is not associative');
        }
    }
}
