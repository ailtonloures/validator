<?php

namespace Validator;

use Exception;
use Validator\ValidationTrait;

/**
 * Class to manage the validation
 *
 * @package Validator
 * @author Ailton Loures <ailton.loures99@gmail.com>
 * @copyright 2020 Validator
 * @version 1.2.4
 * @see https://github.com/ailtonloures/validator
 */
final class Validator
{
    use ValidationTrait;

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
     * @param array $target The target to be validated. Ex: requests, forms, etc.
     * @param array $rules The rules for each target die
     * @param array|null $messages The custom message for each rule
     * @param array|null $attributes Renames target data
     * @param string|null $nickName A nickname for the validated dataset
     *
     * @return Validator
     * @throws Exception
     */
    public static function make(array $target, array $rules, array $messages = null, array $attributes = null, string $nickName = null): Validator
    {
        if (!empty($rules) && empty($target)) {
            throw new Exception("This target to be validated is null.");
        }

        foreach ($rules as $attribute => $rule) {

            if (!array_key_exists($attribute, $target)) {
                self::setMessage($attribute, 'This attribute not exists.');

            } else {
                $attributeValue = $target[$attribute];
                $attributeName  = $attributes[$attribute] ?? $attribute;

                if (is_object($rule)) {
                    $attributeRules = ["callback_function"];
                } else if (is_array($rule)) {
                    $attributeRules = $rule;
                } else {
                    $attributeRules = explode("|", $rule);
                }

                foreach ($attributeRules as $newValidation => $validation) {
                    if (!is_object($validation)) {
                        $separatedValidations = explode(":", $validation);
                        $validationName       = trim(reset($separatedValidations));
                        $extraAttribute       = !is_object($rule) ? end($separatedValidations) : $rule;

                    } else {
                        $validationName = "callback_function";
                        $extraAttribute = $validation;
                    }

                    $validationMessage = null;

                    if (!empty($messages)) {

                        if (key_exists($attribute, $messages)) {
                            $inputMessages = $messages[$attribute];

                            foreach ($inputMessages as $inputMessage => $message) {
                                if ($inputMessage == (is_string($newValidation) ? $newValidation : $validationName)) {
                                    $validationMessage = self::filterMessage($attributeName, $message, $attributeValue, $extraAttribute);
                                }
                            }
                        }

                        $keys               = array_keys($messages);
                        $genericValidations = array_values(array_filter($keys, function ($key) {
                            return strstr($key, "*.");
                        }));

                        foreach ($genericValidations as $genericValidation) {
                            $separatedGenericValidation = explode(".", $genericValidation);
                            $genericValidationName      = end($separatedGenericValidation);

                            if ($genericValidationName == $validationName) {
                                $validationMessage = self::filterMessage($attributeName, $messages[$genericValidation], $attributeValue, $extraAttribute);
                                $validationName    = $genericValidationName;
                            }
                        }
                    }

                    self::$messageNickName = $nickName;
                    self::{$validationName}($attributeName, $validationMessage, $attributeValue, $extraAttribute, $target);
                }
            }
        }

        return new static();
    }

    /**
     * Saves a new message for validation
     *
     * @param string $attribute The attribute name
     * @param string $message The message for the input
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
     * @param string $message
     * @param mixed $attributeValue
     * @param mixed $extraAttribute
     * @return string
     */
    protected static function filterMessage(string $attribute, string $message, $attributeValue, $extraAttribute = null): string
    {
        $message = str_replace(":attr", $attribute, $message);

        if (!is_object($attributeValue)) {
            $message = str_replace(":value", $attributeValue, $message);
        }

        if (!empty($extraAttribute) && !is_object($extraAttribute)) {
            $message = str_replace(":min", $extraAttribute, $message);
            $message = str_replace(":max", $extraAttribute, $message);
        }

        return $message;
    }

    /**
     * Returns all messages if there is any failed validation
     *
     * @return array|null
     */
    public static function fails(): ?array
    {
        if (!self::valid()) {
            return ['validation' => self::getMessages()];
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

}
