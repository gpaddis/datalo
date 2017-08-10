<?php namespace Dataloader\Parsers;

class IsbnParser extends Parser
{
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
