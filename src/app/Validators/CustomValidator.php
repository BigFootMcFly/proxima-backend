<?php

namespace App\Validators;

use Closure;
use Illuminate\Support\Facades\Validator;

abstract class CustomValidator
{
    public static string $rule = '';
    public static string $message = 'The :attribute field failed validation.';

    public static function getExtension(): Closure
    {
        return static::extension(...);
    }

    public static function registerValidator()
    {

        Validator::extend(
            rule: static::$rule,
            extension: static::getExtension(),
            message: static::$message
        );

    }

    abstract public static function extension($attribute, $value, $parameters, $validator): bool;

}
