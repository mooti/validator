<?php
/**
 * Boolean Validator
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

class BooleanValidator extends AbstractTypeValidator
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
        if (!is_bool($data)) {
            $message = $constraints['message'] ?? '%s must be boolean';
            throw new DataValidationException(sprintf($message, $prettyName));
        }

        parent::validate($constraints, $data, $prettyName);
    }
}
