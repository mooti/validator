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

class AbstractTypeValidator implements TypeValidatorInterface
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
        if (isset($constraints['callback'])) {     
            call_user_func_array($constraints['callback'], array($data, $prettyName));
        }
    }
}
