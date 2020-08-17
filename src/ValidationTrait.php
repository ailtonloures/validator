<?php

namespace Validator;

/**
 * Trait to valid the attributeValues
 *
 * @author Ailton Loures <ailton.loures99@gmail.com>
 * @version 1.2.5
 * @copyright 2020 Validator
 * @package Validator
 * @see https://github.com/ailtonloures/validator
 */
trait ValidationTrait
{
    /**
     * Valid the e-mails attribute
     *
     * @param string $attribute The name of the attribute
     * @param string|null $message The message validation for the attribute
     * @param string|null $value The value of the attribute
     *
     * @return void
     */
    protected static function email(string $attribute, ?string $message = null, ?string $value = null): void
    {
        if ($value) {
            $email = filter_var($value, FILTER_SANITIZE_EMAIL);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                self::setMessage($attribute, $message ?? 'Invalid e-mail.');
            }
        }
    }

    /**
     * Validates if the attribute is empty or null
     *
     * @param string $attribute The name of the attribute
     * @param string|null $message The message validation for the attribute
     * @param string|null $value The value of the attribute
     *
     * @return void
     */
    protected static function required(string $attribute, ?string $message = null, ?string $value = null): void
    {
        if (empty($value) || $value === "") {
            self::setMessage($attribute, $message ?? 'Required field.');
        }

    }

    /**
     * Validates if the attribute is numeric
     *
     * @param string $attribute The name of the attribute
     * @param string|null $message The message validation for the attribute
     * @param string|null $value The value of the attribute
     *
     * @return void
     */
    protected static function numeric(string $attribute, ?string $message = null, $value = null): void
    {
        if ($value && !is_numeric($value)) {
            self::setMessage($attribute, $message ?? 'Not a valid number');
        }

    }

    /**
     * Validates if the attribute value exceeds the maximum number of characters set
     *
     * @param string $attribute The name of the attribute
     * @param string|null $message The message validation for the attribute
     * @param string|null $value The value of the attribute
     * @param integer|null $max The maximum character value
     *
     * @return void
     */
    protected static function max(string $attribute, ?string $message = null, $value = null, ?int $max = null): void
    {
        if ($value && strlen($value) > $max) {
            self::setMessage($attribute, $message ?? "This field must have a maximum of {$max} characters");
        }

    }

    /**
     * Validates if the attribute value is less than the minimum number of characters set
     *
     * @param string $attribute The name of the attribute
     * @param string|null $message The message validation for the attribute
     * @param string|null $value The value of the attribute
     * @param integer|null $min The minimum character value
     *
     * @return void
     */
    protected static function min(string $attribute, ?string $message = null, $value = null, ?int $min = null): void
    {
        if ($value && strlen($value) < $min) {
            self::setMessage($attribute, $message ?? "This field must be at least {$min} characters");
        }
    }

    /**
     * Executes and validates if the callback is returning a false condition,
     * if it is false, it will return the message that the result is invalid
     *
     * Returns three parameters, the first is the value of the attribute,
     * the second is the name of the attribute itself and
     * the third is the target that has been validated and all its data
     *
     * @param string $attribute The name of the attribute
     * @param string|null $message The message validation for the attribute
     * @param string|null $value The value of the attribute
     * @param object|null $callback The function will be executed
     * @param array|null $data It is the target itself that has been validated, which can be a request, form, etc.
     *
     * @return void
     */
    protected static function callback_function(string $attribute, ?string $message = null, $value = null, ?object $callback = null, ?array $data = null): void
    {
        if ($value && is_object($callback)) {
            $callbackFunctionReturn = call_user_func($callback, $value, $attribute, $data);

            if ($callbackFunctionReturn === false) {
                self::setMessage($attribute, $message ?? "This function not have a message.");
            }
        }
    }
}
