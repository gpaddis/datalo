<?php namespace Dataloader\Validators;

/**
 * Abstract validator class: $this->isValid() returns true if the identifier
 * passes validation, false if it doesn't.
 */
abstract class Validator
{
    /**
     * Validator constructor.
     *
     * @param  string $identifier
     * @return Validator
     */
    public static function make()
    {
        return new static;
    }

    /**
     * Check if an identifier is valid.
     *
     * @param  string $identifier
     * @return boolean
     */
    public function validate($identifier)
    {
    }

    /**
     * Stripes out dashes and whitespaces.
     *
     * @param  string $identifier
     * @return string
     */
    public function clean($identifier)
    {
        return str_replace(['-', ' '], '', $identifier);
    }
}
