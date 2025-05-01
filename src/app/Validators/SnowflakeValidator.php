<?php

namespace App\Validators;

class SnowflakeValidator extends CustomValidator
{

    public static string $rule = 'snowflake';

    public static string $message = 'The :attribute field must be a !!! valid snowflake.';

    public static function extension($attribute, $value, $parameters, $validator): bool
    {

        return preg_match('/\d{18}/', $value) === 1;

    }

}
