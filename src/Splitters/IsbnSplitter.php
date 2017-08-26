<?php namespace Dataloader\Splitters;

class IsbnSplitter extends Splitter
{
	
	/**
     * Split a string field containing one or multiple ISBNs separated by non-numeric & non-dash characters.
     *
     * @param  string $field
     * @return array
     */
    public function split(string $field = '') : array
    {
        $allIsbns = preg_split("/[^\d-x]/i", $field);

        return array_values(array_filter($allIsbns));
    }
}