<?php namespace Dataloader\Parsers;

class IsbnParser extends Parser
{
    /**
     * Split a string containing one or multiple ISBNs separated by specific characters.
     *
     * @param  string $isbns
     * @param  string $separators
     * @return array
     */
    public static function split($isbns = '')
    {
        $allIsbns = preg_split("/[^\d-x]/i", $isbns);

        $nonEmptyIsbns = array_values(array_filter($allIsbns));

        return $nonEmptyIsbns;
    }
}
