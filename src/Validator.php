<?php

namespace Validator;

use Exception;
use Validator\ValidationTrait;

final class Validator
{
    use ValidationTrait;

    /** @var array $messages */
    protected static $messages;

    /** @var string $messageNickName */
    protected static $messageNickName;

    /**
     * @param array $target
     * @param array $rules
     * @param array|null $messages
     * @param array|null $attributes
     * @param string|null $nickName
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

                if (!is_object($rule) && !is_array($rule)) {
                    $inputRules = explode("|", $rule);
                }

                if (is_object($rule)) {
                    $inputRules = ["callback_function"];
                }

                if (is_array($rule)) {
                    $inputRules = $rule;
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
     * @param string $input
     * @param string $message
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