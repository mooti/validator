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

use Mooti\Xizlr\Testable\Testable;
use Mooti\Validator\Exception\DataValidationException;

class ArrayValidator implements TypeValidatorInterface
{
    use Testable;
    
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
        if (gettype($data) == 'array') {
            return $this->validateSequantialArray($data);
        } else {
            throw new DataValidationException('This value must be a sequential array');
        }
    }

    public function validateSequantialArray(array $data)
    {
        //if the array has to have keys that are both numeric AND sequential
        if (!empty($data) && array_keys($data) !== range(0, count($data) - 1)) {
            throw new DataValidationException('This value is an array but it is not sequential');
        }
        return true;
    }
}
