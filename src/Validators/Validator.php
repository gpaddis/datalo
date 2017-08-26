<?php namespace Dataloader\Validators;

/**
 * Abstract validator class. $this->validate() returns true if the identifier
 * passes validation, false if it doesn't.
 */
abstract class Validator
{
    abstract public function validate(string $identifier) : bool;

    /**
     * Validator static constructor.
     *
     * @return Validator
     */
    public static function make()
    {
        return new static;
    }

    /**
     * Stripe out dashes and whitespaces.
     *
     * @param  string $identifier
     * @return string
     */
    public function clean($identifier)
    {
        return str_replace(['-', ' '], '', $identifier);
    }
}
