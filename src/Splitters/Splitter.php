<?php

namespace Dataloader\Splitters;

class Splitter
{
    /**
     * Split a string field containing one or multiple ISBNs or ISSNs
     * separated by non-numeric, non-X & non-dash characters.
     *
     * @param string $field
     *
     * @return array
     */
    public function split(string $field = '') : array
    {
        $identifiers = preg_split("/[^\d-x]/i", $field);

        return array_values(array_filter($identifiers));
    }
}
