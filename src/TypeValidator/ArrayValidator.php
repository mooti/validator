<?php
/**
 * Array Validator
 *
 * Array Validator class
 *
 * @package      Mooti
 * @subpackage   Validator
 * @author       Ken Lalobo <ken@mooti.io>
 */

namespace Mooti\Validator\TypeValidator;

use Mooti\Factory\Factory;
use Mooti\Validator\Exception\DataValidationException;

class ArrayValidator extends AbstractTypeValidator
{
    use Factory;
    
    /**
     * Validate some data and throw an exception if the data invalid
     *
     * @param array $constraints The rules
     * @param mixed $data        The data to validate
     * @param mixed $prettyName  Human readable name for the data being validated
     *
     * @throws DataValidationException
     */
    public function validate(array $constraints, $data, $prettyName = 'This value')
    {
        if (gettype($data) == 'array') {
            $this->validateSequentialArray($data, $prettyName);
        } else {
            $message = $constraints['message'] ?? '%s must be a sequential array';
            throw new DataValidationException(sprintf($message, $prettyName));
        }

        parent::validate($constraints, $data, $prettyName);
    }

    /**
     * Validate that array is sequential
     *
     * @param array $data The data to validate
     * @param mixed $prettyName  Human readable name for the data being validated
     *
     * @throws DataValidationException
     */
    public function validateSequentialArray(array $data, $prettyName = 'This value')
    {
        //if the array has to have keys that are both numeric AND sequential
        if (!empty($data) && array_keys($data) !== range(0, count($data) - 1)) {
            throw new DataValidationException(sprintf('%s is an array but it is not sequential', $prettyName));
        }
    }
}
