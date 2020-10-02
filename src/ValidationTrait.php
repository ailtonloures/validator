<?php

namespace Validator;

/**
 * Trait to valid the attributeValues
 *
 * @author Ailton Loures <ailton.loures99@gmail.com>
 * @version 1.4
 * @copyright 2020 Validator
 * @package Validator
 * @see https://github.com/ailtonloures/validator
 */
trait ValidationTrait
{
    /**
     * Valid the e-mails attribute
     *
     * @param string $attribute
     * @param string|null $message
     * @param string|null $value
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
     * Valid URL attribute
     *
     * @param string $attribute
     * @param string|null $message
     * @param string|null $value
     * @return void
     */
    protected static function url(string $attribute, ?string $message = null, ?string $value = null): void
    {
        if ($value) {
            $url = filter_var($value, FILTER_SANITIZE_URL);

            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                self::setMessage($attribute, $message ?? 'Invalid URL.');
            }
        }
    }

    /**
     * Validates if the attribute is empty or null
     *
     * @param string $attribute
     * @param string|null $message
     * @param mixed|null $value
     *
     * @return void
     */
    protected static function required(string $attribute, ?string $message = null, $value = null): void
    {
        if (empty($value) || $value === "") {
            self::setMessage($attribute, $message ?? 'Required field.');
        }
    }

    /**
     * Validates if the attribute is numeric
     *
     * @param string $attribute
     * @param string|array|null $message
     * @param string|null $value
     * @param string|null $rules
     *
     * @return void
     */
    protected static function numeric(string $attribute, $message = null, $value = null, ?string $rules = null): void
    {
        if ($value && !is_numeric($value)) {
            self::setMessage($attribute, $message['default'] ?? $message ?? 'Not a valid number.');
        }

        if (is_numeric($value)) {

            if (!empty($rules)) {
                $arrayRules = explode('=', $rules);

                $rule   = reset($arrayRules);
                $values = end($arrayRules);

                if ($rule === 'bt') {
                    $numbers = explode('&', $values);

                    $firstNumber = reset($numbers);
                    $lastNumber  = end($numbers);

                    if ($value < $firstNumber || $value > $lastNumber) {
                        self::setMessage($attribute, $message['bt'] ?? $message ?? "This value must be between {$firstNumber} and {$lastNumber}.");
                    }
                } else if ($rule === 'gte') {
                    if ($value < $values) {
                        self::setMessage($attribute, $message['gte'] ?? $message ?? "This value must be greater than or equal {$values}.");
                    }
                } else if ($rule === 'lte') {
                    if ($value > $values) {
                        self::setMessage($attribute, $message['lte'] ?? $message ?? "This value must be less than or equal {$values}.");
                    }
                }
            }
        }
    }

    /**
     * Validates the corresponding number if it is greater than the maximum value
     *
     * @param string $attribute
     * @param string|null $message
     * @param mixed|null $value
     * @param integer|null $max
     * @return void
     */
    protected static function max_number(string $attribute, ?string $message = null, $value = null, ?int $max = null): void
    {
        if ($value && is_numeric($value) && $value > $max) {
            self::setMessage($attribute, $message ?? "The maximum allowed value is {$max}.");
        }
    }

    /**
     * Validates the corresponding number if it is less than the minimum value
     *
     * @param string $attribute
     * @param string|null $message
     * @param mixed|null $value
     * @param integer|null $min
     * @return void
     */
    protected static function min_number(string $attribute, ?string $message = null, $value = null, ?int $min = null): void
    {
        if ($value && is_numeric($value) && $value < $min) {
            self::setMessage($attribute, $message ?? "The minimum allowed value is {$min}.");
        }
    }

    /**
     * Validates if the attribute value exceeds the maximum number of characters set
     *
     * @param string $attribute
     * @param string|null $message
     * @param string|null $value
     * @param integer|null $max
     *
     * @return void
     */
    protected static function max(string $attribute, ?string $message = null, $value = null, ?int $max = null): void
    {
        if ($value && strlen($value) > $max) {
            self::setMessage($attribute, $message ?? "This field must have a maximum of {$max} characters.");
        }
    }

    /**
     * Validates if the attribute value is less than the minimum number of characters set
     *
     * @param string $attribute
     * @param string|null $message
     * @param string|null $value
     * @param integer|null $min
     *
     * @return void
     */
    protected static function min(string $attribute, ?string $message = null, $value = null, ?int $min = null): void
    {
        if ($value && strlen($value) < $min) {
            self::setMessage($attribute, $message ?? "This field must be at least {$min} characters.");
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
     * @param string $attribute
     * @param string|null $message
     * @param string|null $value
     * @param object|null $callback
     * @param array|null $data
     *
     * @return void
     */
    protected static function callback(string $attribute, ?string $message = null, $value = null, ?object $callback = null, ?array $data = null): void
    {
        if ($value && is_object($callback)) {
            $callbackFunctionReturn = call_user_func($callback, $value, $attribute, $data);

            if ($callbackFunctionReturn === false) {
                self::setMessage($attribute, $message ?? "This function not have a message.");
            }
        }
    }

    /**
     * Validates if the values ​​passed are equal
     *
     * @param string $attribute
     * @param string|null $message
     * @param mixed|null $value
     * @param mixed|null $equalValue
     * @return void
     */
    protected static function equal_to(string $attribute, ?string $message = null, $value = null, $equalValue = null): void
    {
        if ($value && $value != $equalValue) {
            self::setMessage($attribute, $message ?? 'The corresponding value is not the same as the determined value.');
        }
    }

    /**
     * Validates the corresponding date
     *
     * @param string $attribute
     * @param string|null $message
     * @param mixed|null $value
     * @param string|null $rules
     * @return void
     */
    protected static function date(string $attribute, ?string $message = null, $value = null, ?string $rules = null): void
    {
        if ($value) {
            $arrayRules  = explode('=', $rules);
            $rule        = reset($arrayRules);
            $targetValue = end($arrayRules);

            $date = new \DateTime($value);

            if ($rule === 'gte') {
                $targetDate = new \DateTime($targetValue);

                if ($date->format('Y-m-d') < $targetDate->format('Y-m-d')) {
                    self::setMessage($attribute, $message['gte'] ?? $message ?? "The corresponding date must be greater than or equal {$targetDate->format('Y-m-d')}.");
                }
            } else if ($rule === 'lte') {
                $targetDate = new \DateTime($targetValue);

                if ($date->format('Y-m-d') > $targetDate->format('Y-m-d')) {
                    self::setMessage($attribute, $message['lte'] ?? $message ?? "The corresponding date must be less than or equal {$targetDate->format('Y-m-d')}.");
                }
            } else if ($rule === 'bt') {
                $dates = explode('&', $targetValue);

                $firstDate = new \DateTime(reset($dates));
                $lastDate  = new \DateTime(end($dates));

                if ($date->format('Y-m-d') < $firstDate->format('Y-m-d')
                    || $date->format('Y-m-d') > $lastDate->format('Y-m-d')) {
                    self::setMessage($attribute, $message['bt'] ?? $message ?? "The corresponding date must be between {$firstDate->format('Y-m-d')} and {$lastDate->format('Y-m-d')}");
                }
            }
        }
    }
}
