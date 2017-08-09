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
    public static function split($isbns = '', $separators = '')
    {
        $separators = empty($separators) ? '\s|;=' : $separators;

        $allIsbns = preg_split("/[{$separators}]/", $isbns);

        $nonEmptyIsbns = array_values(array_filter($allIsbns));

        return $nonEmptyIsbns;
    }
}
