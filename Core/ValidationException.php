<?php

namespace Core;

class ValidationException extends \Exception
{
    public readonly array $errors;
    public readonly array $old;

    /**
     * @param $errors
     * @param $old
     * @return mixed
     * @throws ValidationException
     */
    public static function throw($errors, $old): mixed
    {
        $instance = new static('The form failed to validate.');

        $instance->errors = $errors;
        $instance->old = $old;

        throw $instance;
    }
}