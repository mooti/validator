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
     * @throws InvalidRuleException
     * @throws DataValidationException
     */
    public function validate(array $constraints, $data, $prettyName = 'This value')
    {
        if (gettype($data) != 'string') {
            $message = $constraints['message'] ?? '%s must be a string';
            throw new DataValidationException(sprintf($message, $prettyName));
        }

        if (isset($constraints['length'])) {
            if (sizeof($constraints['length']) != 2) {
                throw new InvalidRuleException(sprintf('The length property of %s needs to have two members', $prettyName));
            }
            $this->validateLength($data, $constraints['length'][0], $constraints['length'][1], $prettyName);
        }

        if (isset($constraints['enum'])) {
            if (sizeof($constraints['enum']) == 0) {
                throw new InvalidRuleException(sprintf('The enum property of %s needs to have at least one member', $prettyName));
            }
            $this->validateEnum($data, $constraints['enum'], $prettyName);
        }

        if (isset($constraints['regex'])) {
            if (empty($constraints['regex'])) {
                throw new InvalidRuleException(sprintf('The regex property of %s needs to be set', $prettyName));
            }
            $this->validateRegex($data, $constraints['regex'], $prettyName);
        }

        parent::validate($constraints, $data, $prettyName);
    }

    /**
     * @param $data
     * @param null $min
     * @param null $max
     * @param string $prettyName
     * @return bool
     * @throws DataValidationException
     */
    public function validateLength($data, $min = null, $max = null, $prettyName = 'This value')
    {
        $length = strlen($data);

        if (isset($min) && $length < $min) {
            throw new DataValidationException(sprintf('%s must have a length of at least %d', $prettyName, $min));
        }

        if (isset($max) && $length > $max) {
            throw new DataValidationException(sprintf('%s must have a length less than or equal to %d', $prettyName, $max));
        }

        return true;
    }

    /**
     * @param $data
     * @param array $enum
     * @param string $prettyName
     * @return bool
     * @throws DataValidationException
     */
    public function validateEnum($data, array $enum, $prettyName = 'This value')
    {
        if (in_array($data, $enum, true) == false) {
            throw new DataValidationException(
                sprintf(
                    '%s is not an allowed value for %s. Allowed values are: %s',
                    $data,
                    $prettyName,
                    implode(', ', $enum)
                )
            );
        }

        return true;
    }

    /**
     * @param $data
     * @param $regex
     * @param $prettyName
     * @return bool
     * @throws DataValidationException
     * @throws InvalidRuleException
     */
    public function validateRegex($data, $regex, $prettyName = 'This value')
    {
        $match = @preg_match($regex, $data);
        if ($match === 0) {
            throw new DataValidationException(
                sprintf(
                    '%s is not an allowed value for %s. It does not match the regex: %s',
                    $data,
                    $prettyName,
                    $regex
                )
            );
        } elseif ($match === false) {
            throw new InvalidRuleException(sprintf('An error occured. The regex for %s may be invalid', $prettyName));
        }
        return true;
    }
}
