<?php

namespace Validator;

trait ValidationTrait
{
    /**
     * @param string $input
     * @param string $message
     * @param string $value
     * @return void
     */
    protected static function email(string $input, string $message = null, string $value = null): void
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            self::setMessage($input, $message ?? 'Invalid e-mail.');
        }

    }

    /**
     * @param string $input
     * @param string $message
     * @param string $value
     * @return void
     */
    protected static function required(string $input, string $message = null, string $value = null): void
    {
        if (empty($value)) {
            self::setMessage($input, $message ?? 'Required field.');
        }

    }

    /**
     * @param string $input
     * @param string $message
     * @param integer|float $value
     * @return void
     */
    protected static function numeric(string $input, string $message = null, $value = null): void
    {
        if (!is_numeric($value)) {
            self::setMessage($input, $message ?? 'Not a valid number');
        }

    }

    /**
     * @param string $input
     * @param string $message
     * @param mixed $value
     * @param integer $max
     * @return void
     */
    protected static function max(string $input, string $message = null, $value = null, int $max = null): void
    {
        if (strlen($value) > $max) {
            self::setMessage($input, $message ?? "This field must have a maximum of {$max} characters");
        }

    }

    /**
     * @param string $input
     * @param string $message
     * @param mixed $value
     * @param integer $min
     * @return void
     */
    protected static function min(string $input, string $message = null, $value = null, int $min = null): void
    {
        if (strlen($value) < $min) {
            self::setMessage($input, $message ?? "This field must be at least {$min} characters");
        }
    }

    /**
     * @param string $input
     * @param string $message
     * @param mixed $value
     * @param object $callback
     * @return void
     */
    protected static function callback_function(string $input, string $message = null, $value = null, object $callback = null): void
    {
        if (is_object($callback)) {
            $callbackFunctionReturn = call_user_func($callback, $value, $input);

            if ($callbackFunctionReturn === false) {
                self::setMessage($input, $message ?? "This function not have a message.");
            }
        }
    }
}
