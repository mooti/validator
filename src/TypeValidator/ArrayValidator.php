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

class ArrayValidator implements TypeValidatorInterface
{
    use Factory;
    
    /**
     * Validate some data and throw an exception if the data invalid
     *
     * @param array $constraints The rules
     * @param mixed $data        The data to validate
     *
     * @throws DataValidationException
     */
    public function validate(array $constraints, $data)
    {
        if (gettype($data) == 'array') {
            $this->validateSequentialArray($data);
        } else {
            throw new DataValidationException('This value must be a sequential array');
        }
    }

    /**
     * Validate that array is sequential
     *
     * @param array $data The data to validate
     *
     * @throws DataValidationException
     */
    public function validateSequentialArray(array $data)
    {
        //if the array has to have keys that are both numeric AND sequential
        if (!empty($data) && array_keys($data) !== range(0, count($data) - 1)) {
            throw new DataValidationException('This value is an array but it is not sequential');
        }
    }
}
