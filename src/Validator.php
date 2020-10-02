<?php

namespace Validator;

/**
 * Class to manage the validation
 *
 * @package Validator
 * @author Ailton Loures <ailton.loures99@gmail.com>
 * @copyright 2020 Validator
 * @version 1.4
 * @see https://github.com/ailtonloures/validator
 */
final class Validator
{
    use \Validator\ValidationTrait;

    /**
     * The messages
     *
     * Store validation messages
     *
     * @var array $messages
     */
    protected static $messages;

    /**
     * Message NickName
     *
     * Nickname for the message set
     *
     * @var string $messageNickName
     */
    protected static $messageNickName;

    /**
     * Create and execute validations
     *
     * @param array $target
     * @param array $rules
     * @param array|null $messages
     * @param array|null $attributes
     * @param string|null $nickName
     *
     * @return Validator
     * @throws Exception
     */
    public static function make(
        array $target,
        array $rules,
        array $messages = null,
        array $attributes = null,
        string $nickName = null
    ): Validator {

        if (empty($rules) && empty($target)) {
            throw new \Exception("This target to be validated is null.");
        }

        foreach ($rules as $attribute => $rule) {

            if (!array_key_exists($attribute, $target)) {
                self::setMessage($attribute, 'This attribute not exists.');
            } else {
                $attributeValue = $target[$attribute];
                $attributeName  = $attributes[$attribute] ?? $attribute;

                if (is_object($rule)) {
                    $attributeRules = ["callback"];
                } else if (is_array($rule)) {
                    $attributeRules = $rule;
                } else {
                    $attributeRules = explode("|", $rule);
                }

                foreach ($attributeRules as $newValidation => $validation) {
                    if (!is_object($validation)) {
                        $separatedValidations = explode(":", $validation);
                        $validationName       = trim(reset($separatedValidations));
                        $extraValue           = !is_object($rule) ? end($separatedValidations) : $rule;
                    } else {
                        $validationName = "callback";
                        $extraValue     = $validation;
                    }

                    $validationMessage = null;

                    if (!empty($messages)) {

                        if (key_exists($attribute, $messages)) {
                            $inputMessages = $messages[$attribute];

                            foreach ($inputMessages as $inputMessage => $message) {
                                if ($inputMessage == (is_string($newValidation) ? $newValidation : $validationName)) {
                                    $validationMessage = self::filterMessage($attributeName, $message, $validationName, $attributeValue, $extraValue);
                                }
                            }
                        }

                        $messageKeys        = array_keys($messages);
                        $genericValidations = array_values(array_filter($messageKeys, function ($key) {
                            return strstr($key, ".");
                        }));

                        foreach ($genericValidations as $genericValidation) {
                            $separatedGenericValidation = explode(".", $genericValidation);
                            $genericValidationName      = reset($separatedGenericValidation);
                            $extraValidation            = end($separatedGenericValidation);

                            if ($genericValidationName == $validationName) {
                                $extraValuesArray   = explode('=', $extraValue);
                                $extraValueSelected = reset($extraValuesArray);

                                if (end($separatedGenericValidation) === "*" || $extraValueSelected == $extraValidation) {
                                    $validationMessage = self::filterMessage($attributeName, $messages[$genericValidation], $validationName, $attributeValue, $extraValue);
                                    $validationName    = $genericValidationName;
                                }
                            }
                        }
                    }

                    self::$messageNickName = $nickName;
                    self::{$validationName}($attribute, $validationMessage, $attributeValue, $extraValue, $target);
                }
            }
        }

        return new static();
    }

    /**
     * Saves a new message for validation
     *
     * @param string $attribute
     * @param string $message
     *
     * @return void
     */
    protected static function setMessage(string $attribute, string $message): void
    {
        $attribute = trim($attribute);

        if ($nickName = self::$messageNickName) {
            self::$messages[trim($nickName)][$attribute] = $message;
            return;
        }

        self::$messages[$attribute] = $message;
    }

    /**
     * Returns all failed validation messages
     *
     * @return array|null
     */
    protected static function getMessages(): ?array
    {
        if (!empty(self::$messages)) {
            return self::$messages;
        }

        return null;
    }

    /**
     * Filter messages and substuition of paramaters
     *
     * @param string $attribute
     * @param string|array $message
     * @param string $rule
     * @param mixed $attributeValue
     * @param mixed $extraValue
     * @return string|array
     */
    protected static function filterMessage(string $attribute, $message, string $rule, $attributeValue, $extraValue = null)
    {
        $message = str_replace(":attr", $attribute, $message);

        if (!is_object($attributeValue)) {
            $message = str_replace(":value", $attributeValue, $message);
        }

        if (!empty($extraValue) && !is_object($extraValue)) {

            if ($rule === "min") {
                $message = str_replace(":min", $extraValue, $message);
            }

            if ($rule === "max") {
                $message = str_replace(":max", $extraValue, $message);
            }

            if ($rule === "numeric") {
                $extraValueSeparated = explode("=", $extraValue);
                $firstValue          = reset($extraValueSeparated);
                $lastValue           = end($extraValueSeparated);

                if ($firstValue === "gte") {
                    $message = str_replace(":gte", $lastValue, $message);
                }

                if ($firstValue === "lte") {
                    $message = str_replace(":lte", $lastValue, $message);
                }

                if ($firstValue === "bt") {
                    $numbers = explode("&", $lastValue);

                    $firstNumber = reset($numbers);
                    $lastNumber  = end($numbers);

                    $message = str_replace(":first", $firstNumber, $message);
                    $message = str_replace(":last", $lastNumber, $message);
                }
            }

            if ($rule === "date") {
                $extraValueSeparated = explode("=", $extraValue);
                $firstValue          = reset($extraValueSeparated);
                $lastValue           = end($extraValueSeparated);

                if ($firstValue === "gte" || $firstValue === "lte") {
                    $formatString = explode(" ", strstr($message, '|format:'));
                    $formatString = reset($formatString);
                    $arrayString  = explode(":", $formatString);
                    $format       = end($arrayString);

                    $date = !empty($formatString) ? (new \DateTime($lastValue))->format($format) : $lastValue;

                    $message = str_replace(":date{$formatString}", $date, $message);
                }

                if ($firstValue === "bt") {
                    $dates = explode("&", $lastValue);

                    $firstValue = reset($dates);
                    $lastValue  = end($dates);

                    $formatString = explode(" ", strstr($message, '|format:'));
                    $formatString = reset($formatString);
                    $arrayString  = explode(":", $formatString);
                    $format       = end($arrayString);

                    $firstDate = !empty($formatString) ? (new \DateTime($firstValue))->format($format) : $firstValue;
                    $lastDate  = !empty($formatString) ? (new \DateTime($lastValue))->format($format) : $lastValue;

                    $message = str_replace(":first{$formatString}", $firstDate, $message);
                    $message = str_replace(":last{$formatString}", $lastDate, $message);
                }
            }

            if ($rule === "equal_to") {
                $message = str_replace(":equal_to", $extraValue, $message);
            }

            if ($rule === "max_number") {
                $message = str_replace(":max_number", $extraValue, $message);
            }

            if ($rule === "min_number") {
                $message = str_replace(":min_number", $extraValue, $message);
            }
        }

        return $message;
    }

    /**
     * Returns all messages if there is any failed validation
     *
     * @param bool $json
     * @return array|object|null
     */
    public static function fails(bool $json = false)
    {
        if (!self::valid()) {

            $validationMessagesArray = ['validation' => self::getMessages()];

            if ($json === true) {
                return json_encode($validationMessagesArray);
            }

            return $validationMessagesArray;
        }

        return null;
    }

    /**
     * Returns true if there is no fault message
     *
     * @return boolean
     */
    public static function valid(): bool
    {
        return empty(self::getMessages());
    }

    /**
     * Returns true if there is fault message
     *
     * @return boolean
     */
    public static function invalid(): bool
    {
        return !empty(self::getMessages());
    }
}
