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

class NumberValidator implements TypeValidatorInterface
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
        if (is_numeric($data) == false) {
            throw new DataValidationException('This value must be a number');
        }

        if (isset($constraints['integer'])) {
            $this->validateInteger($data, $constraints['integer']);
        }
    }

    /**
     * Validate that the data is/is not an integer
     *
     * @param array $data The data to validate
     *
     * @throws DataValidationException
     */
    public function validateInteger($data, $isInt)
    {
        if ($isInt != is_int($data)) {
            throw new DataValidationException('This value must '.($isInt == false?'not':'').' be an integer');
        }
    }
}
