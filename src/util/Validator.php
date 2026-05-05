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
}
