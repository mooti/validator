<?php
/**
 * Number Validator
 *
 * Number Validator class
 *
 * @package      Mooti
 * @subpackage   Validator
 * @author       Ken Lalobo <ken@mooti.io>
 */

namespace Mooti\Validator\TypeValidator;

use Mooti\Factory\Factory;
use Mooti\Validator\Exception\DataValidationException;

class NumberValidator extends AbstractTypeValidator
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
        if (is_numeric($data) == false) {
            $message = $constraints['message'] ?? '%s must be a number';
            throw new DataValidationException(sprintf($message, $prettyName));
        }

        if (isset($constraints['integer'])) {
            $this->validateInteger($data, $constraints['integer'], $prettyName);
        }

        parent::validate($constraints, $data, $prettyName);
    }

    /**
     * Validate that the data is/is not an integer
     *
     * @param array $data The data to validate
     * @param mixed $prettyName  Human readable name for the data being validated
     *
     * @throws DataValidationException
     */
    public function validateInteger($data, $isInt, $prettyName = 'This value')
    {
        if ($isInt != is_int($data)) {
            throw new DataValidationException(sprintf('%s must '.($isInt == false?'not':'').' be an integer', $prettyName));
        }
    }
}
