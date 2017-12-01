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

class ObjectValidator extends AbstractTypeValidator
{
    use Factory;
    
    /**
     * Validate some data
     *
     * @param array $constraints The rules
     * @param mixed $data        The data to validate
     * @param mixed $prettyName  Human readable name for the data being validated
     *
     * @return boolean Wether it was valid or not
     */
    public function validate(array $constraints, $data, $prettyName = 'This value')
    {
        if (gettype($data) == 'object') {
            $this->validateObject($data);
        } elseif (gettype($data) == 'array') {
            $this->validateAssociativeArray($data, $prettyName);
        } else {
            $message = $constraints['message'] ?? '%s must be a standard object or an associative array';
            throw new DataValidationException(sprintf($message, $prettyName));
        }

        parent::validate($constraints, $data, $prettyName);
    }

    /**
     * Validate that the data is an instance of the stdClass
     *
     * @param object $data The data to validate
     * @param mixed $prettyName  Human readable name for the data being validated
     *
     * @throws DataValidationException
     */
    public function validateObject($data, $prettyName = 'This value')
    {
        if (!$data instanceof \stdClass) {
            throw new DataValidationException(sprintf('%s must be an instance of the stdClass', $prettyName));
        }
    }

    /**
     * Validate that the data is an associative array
     *
     * @param array $data The data to validate
     * @param mixed $prettyName  Human readable name for the data being validated
     *
     * @throws DataValidationException
     */
    public function validateAssociativeArray(array $data, $prettyName = 'This value')
    {
        //if the array has keys that are both numeric AND sequential then it is not associative
        if (!empty($data) && array_keys($data) === range(0, count($data) - 1)) {
            throw new DataValidationException(sprintf('%s is an array but it is not associative', $prettyName));
        }
    }
}
