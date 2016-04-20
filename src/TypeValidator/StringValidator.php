<?php
/**
 * String Validator
 *
 * String Validator class
 *
 * @package      Mooti
 * @subpackage   Validator
 * @author       Ken Lalobo <ken@mooti.io>
 */

namespace Mooti\Validator\TypeValidator;

use Mooti\Factory\Factory;
use Mooti\Validator\Exception\DataValidationException;
use Mooti\Validator\Exception\InvalidRuleException;

class StringValidator implements TypeValidatorInterface
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
        if (gettype($data) != 'string') {
            throw new DataValidationException('This value must be a string');
        }

        if (isset($constraints['length'])) {
            if (sizeof($constraints['length']) != 2) {
                throw new InvalidRuleException('The length property needs to have two members');
            }
            $this->validateLength($data, $constraints['length'][0], $constraints['length'][1]);
        }       
    }

    public function validateLength($data, $min = null, $max = null)
    {
        $length = strlen($data);

        if (isset($min) && $length < $min) {
            throw new DataValidationException('This value must have a length of at least '.$min);
        }

        if (isset($max) && $length > $max) {
            throw new DataValidationException('This value must have a length less than or equal to '.$max);
        }

        return true;
    }
}
