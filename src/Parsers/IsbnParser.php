<?php namespace Dataloader\Parsers;

use Dataloader\Validators\IsbnValidator;

class IsbnParser extends Parser
{
    protected $validator;

    /**
     * IsbnParser Constructor.
     *
     * @param Reader $reader
     */
    public function __construct($reader)
    {
        parent::__construct($reader);
        $this->validator = IsbnValidator::make();
    }

    /**
     * Split a string containing one or multiple ISBNs separated by non-numeric & non-dash characters.
     *
     * @param  string $isbns
     * @return array
     */
    public static function split(string $isbns = '')
    {
        $allIsbns = preg_split("/[^\d-x]/i", $isbns);

        return array_values(array_filter($allIsbns));
    }
}
