<?php

namespace Validator;

/**
 * Trait to valid the inputValues
 *
 * @package Validator
 * @author Ailton Loures <ailton.loures99@gmail.com>
 * @copyright 2020 Validator
 * @version 1.2.4
 * @see https://github.com/ailtonloures/validator
 */
trait ValidationTrait
{
    /**
     * Valid the e-mails input
     *
     * @param string $input The name of the input
     * @param string|null $message The message validation for the input
     * @param string|null $value The value of the input
     *
     * @return void
     */
    protected static function email(string $input, ?string $message = null, ?string $value = null): void
    {
        $email = trim(filter_var($value, FILTER_SANITIZE_EMAIL));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            self::setMessage($input, "email", $message ?? 'Invalid e-mail.');
        }

    }

    /**
     * Validates if the input is empty or null
     *
     * @param string $input The name of the input
     * @param string|null $message The message validation for the input
     * @param mixed|null $value The value of the input
     *
     * @return void
     */
    protected static function required(string $input, ?string $message = null, $value = null): void
    {
        if (is_string($value)) {
            $value = trim($value);
        }

        if (empty($value) || $value === "") {
            self::setMessage($input, "required", $message ?? 'Required field.');
        }

    }

    /**
     * Validates if the input is numeric
     *
     * @param string $input The name of the input
     * @param string|null $message The message validation for the input
     * @param string|null $value The value of the input
     *
     * @return void
     */
    protected static function numeric(string $input, ?string $message = null, $value = null): void
    {
        if (!is_numeric($value)) {
            self::setMessage($input, "numeric", $message ?? 'Not a valid number');
        }

    }

    /**
     * Validates if the input value exceeds the maximum number of characters set
     *
     * @param string $input The name of the input
     * @param string|null $message The message validation for the input
     * @param mixed|null $value The value of the input
     * @param integer|null $max The maximum character value
     *
     * @return void
     */
    protected static function max(string $input, ?string $message = null, $value = null, ?int $max = null): void
    {
        $value = trim($value);

        if (strlen($value) > $max) {
            self::setMessage($input, "max", $message ?? "This field must have a maximum of {$max} characters");
        }

    }

    /**
     * Validates if the input value is less than the minimum number of characters set
     *
     * @param string $input The name of the input
     * @param string|null $message The message validation for the input
     * @param mixed|null $value The value of the input
     * @param integer|null $min The minimum character value
     *
     * @return void
     */
    protected static function min(string $input, ?string $message = null, $value = null, ?int $min = null): void
    {
        $value = trim($value);

        if (strlen($value) < $min) {
            self::setMessage($input, "min", $message ?? "This field must be at least {$min} characters");
        }
    }

    /**
     * Executes and validates if the callback is returning a false condition,
     * if it is false, it will return the message that the result is invalid
     *
     * Returns three parameters, the first is the value of the input,
     * the second is the name of the input itself and
     * the third is the target that has been validated and all its data
     *
     * @param string $input The name of the input
     * @param string|null $message The message validation for the input
     * @param string|null $value The value of the input
     * @param object|null $callback The function will be executed
     * @param array|null $data It is the target itself that has been validated, which can be a request, form, etc.
     *
     * @return void
     */
    protected static function callback_function(string $input, ?string $message = null, $value = null, ?object $callback = null, ?array $data = null): void
    {
        if (is_object($callback)) {
            $callbackFunctionReturn = call_user_func($callback, $value, $input, $data);

            if ($callbackFunctionReturn === false) {
                self::setMessage($input, "callback", $message ?? "This function not have a message.");
            }
        }
    }
}
