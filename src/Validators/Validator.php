<?php namespace Dataloader\Validators;

use Dataloader\Interfaces\ValidatorInterface;

/**
 * Abstract validator class. $this->validate() returns true if the identifier
 * passes validation, false if it doesn't.
 */
abstract class Validator implements ValidatorInterface
{
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
     * Check if an identifier is valid.
     *
     * @param  string $identifier
     * @return boolean
     */
    abstract public function validate(string $identifier);

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
