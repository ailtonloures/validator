<?php

namespace Validator;

use Exception;
use Validator\ValidationTrait;

/**
 * Class to manage the validation
 *
 * @author Ailton Loures <ailton.loures99@gmail.com>
 * @version 1.2.1
 * @copyright 2020 Validator
 * @package Validator
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

                self::setMessage($input, 'This input not exists.');

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
     * @param string $message The message for the input
     *
     * @return void
     */
    protected static function setMessage(string $input, string $message): void
    {
        if ($nickName = self::$messageNickName) {
            self::$messages[$nickName][$input] = $message;
            return;
        }

        self::$messages[$input] = $message;
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
        if (!empty($messages = self::getMessages())) {
            return ['validation' => $messages];
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
        if (empty(self::getMessages())) {
            return true;
        }

        return false;
    }

}