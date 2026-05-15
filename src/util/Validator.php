<?php

class Validator
{
    public static function validInt(string $values, int $min, int $max): bool
    {
        $valueArray = explode(',', $values);
        foreach ($valueArray as $value) {
            if (!is_numeric($value)) {
                return false;
            }
            if ($value < $min || $value > $max) {
                return false;
            }
        }
        return true;
    }

    public static function validString(string $values, array $acceptedValues): bool
    {
        $valueArray = explode(',', $values);
        foreach ($valueArray as $value) {
            if (!in_array($value, $acceptedValues, true)) {
                return false;
            }
        }
        return true;
    }

    public static function isCommaSeparatedIntegers(string $value): bool
    {
        return (bool) preg_match('/^\d+(,\d+)*$/', trim($value));
    }

    public static function isCommaSeparatedStrings(string $value): bool
    {
        // \p{L} = any unicode letter (covers î, ă, ș, â, etc.)
        // \p{N} = any unicode number
        // \s    = spaces within a value (e.g. "În familie")
        return (bool) preg_match('/^[\p{L}\p{N}\s_]+(,[\p{L}\p{N}\s_]+)*$/u', trim($value));
    }
}
