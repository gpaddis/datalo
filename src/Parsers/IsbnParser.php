<?php namespace Dataloader\Parsers;

use Dataloader\Validators\IsbnValidator;

class IsbnParser extends Parser
{
    protected $reader;
    protected $validator;

    public function __construct($reader)
    {
        $this->reader = $reader;
        $this->validator = IsbnValidator::make();
    }

    /**
     * Split a string containing one or multiple ISBNs separated by non-numeric characters.
     *
     * @param  string $isbns
     * @return array
     */
    public static function split($isbns = '')
    {
        $allIsbns = preg_split("/[^\d-x]/i", $isbns);

        return array_values(array_filter($allIsbns));
    }
}
