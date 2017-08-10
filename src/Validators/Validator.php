<?php namespace Dataloader\Validators;

/**
 * Abstract validator class: $this->isValid() returns true if the identifier
 * passes validation, false if it doesn't.
 */
abstract class Validator
{
    /**
     * The identifier.
     *
     * @var string
     */
    protected $identifier;

    /**
     * Validator constructor.
     *
     * @param string $identifier
     */
    public function __construct(string $identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * Alternative constructor.
     *
     * @param  string $identifier
     * @return Validator
     */
    public static function make($identifier)
    {
        return new static($identifier);
    }

    /**
     * Check if an identifier is valid.
     *
     * @param  string $isbn
     * @return boolean
     */
    public function validate($identifier)
    {
    }

    /**
     * Check if the identifier is valid.
     *
     * @return boolean
     */
    public function isValid()
    {
        return $this->validate($this->identifier);
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

    /**
     * Get the identifier.
     *
     * @return string
     */
    public function get()
    {
        return (string) $this->clean($this->identifier);
    }
}
