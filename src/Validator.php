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
 * @version 1.2.2
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

        foreach ($rules as $input => $rule) {

            if (!array_key_exists($input, $target)) {

                self::setMessage($input, "not_found", 'This input not exists.');

            } else {
                $inputValue     = $target[$input];
                $inputAttribute = $attributes[$input] ?? null;

                if (is_object($rule)) {

                    $inputRules = ["callback_function"];

                } else if (is_array($rule)) {

                    $inputRules = $rule;

                } else {

                    $inputRules = explode("|", $rule);

                }

                foreach ($inputRules as $newFunctionName => $function) {

                    if (!is_object($function)) {

                        $separatedFunction = explode(":", $function);
                        $functionName      = reset($separatedFunction);
                        $extraParam        = !is_object($rule) ? end($separatedFunction) : $rule;

                    } else {

                        $functionName = "callback_function";
                        $extraParam   = $function;

                    }

                    $functionMessage = null;

                    if (!empty($messages) && key_exists($input, $messages)) {

                        $inputMessages = $messages[$input];

                        foreach ($inputMessages as $inputMessage => $message) {

                            if ($inputMessage == (is_string($newFunctionName) ? $newFunctionName : $functionName)) {

                                $message         = str_replace(":attr", $inputAttribute ?? $input, $message);
                                $message         = !is_object($inputValue) ? str_replace(":value", $inputValue, $message) : $message;
                                $functionMessage = $message;

                            }
                        }
                    }

                    self::$messageNickName = $nickName;
                    self::{$functionName}($input, $functionMessage, $inputValue, $extraParam, $target);

                }
            }
        }

        return new static();
    }

    /**
     * Saves a new message for validation
     *
     * @param string $input The input name
     * @param string $messageName The name for the validation message
     * @param string $message The message for the input
     *
     * @return void
     */
    protected static function setMessage(string $input, string $messageName, string $message): void
    {
        if ($nickName = self::$messageNickName) {
            self::$messages[$nickName][$input][$messageName] = $message;
            return;
        }

        self::$messages[$input][$messageName] = $message;
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
