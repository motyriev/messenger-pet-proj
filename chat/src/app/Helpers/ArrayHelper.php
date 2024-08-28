<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class ArrayHelper
{
    public static function convertKeysToCamelCase(array $array): array
    {
        $camelCaseArray = [];

        foreach ($array as $key => $value) {
            $camelCaseKey = Str::camel($key);

            if (is_array($value)) {
                $camelCaseArray[$camelCaseKey] = self::convertKeysToCamelCase($value);
            } else {
                $camelCaseArray[$camelCaseKey] = $value;
            }
        }

        return $camelCaseArray;
    }
}