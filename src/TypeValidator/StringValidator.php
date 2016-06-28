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

class StringValidator extends AbstractTypeValidator
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
        if (gettype($data) != 'string') {
            throw new DataValidationException(sprintf('%s must be a string', $prettyName));
        }

        if (isset($constraints['length'])) {
            if (sizeof($constraints['length']) != 2) {
                throw new InvalidRuleException('The length property needs to have two members');
            }
            $this->validateLength($data, $constraints['length'][0], $constraints['length'][1], $prettyName);
        }

        parent::validate($constraints, $data, $prettyName);
    }

    public function validateLength($data, $min = null, $max = null, $prettyName = 'This value')
    {
        $length = strlen($data);

        if (isset($min) && $length < $min) {
            throw new DataValidationException(sprintf('%s must have a length of at least '.$min, $prettyName));
        }

        if (isset($max) && $length > $max) {
            throw new DataValidationException(sprintf('%s must have a length less than or equal to '.$max, $prettyName));
        }

        return true;
    }
}
