<?php namespace Dataloader\Parsers;

use League\Csv\Reader;

abstract class Parser
{
    protected $reader;
    protected $validator;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
        $this->validator = null;
    }

    public static function make(Reader $reader)
    {
        return new static($reader);
    }

    public static function split($identifiers)
    {
    }

    /**
     * Analyze a row and find the column index(es) containing identifiers.
     *
     * @param  array     $row       A single row converted to array.
     * @param  Validator $validator
     * @return array                An array of the columns containing identifiers.
     */
    public function analyzeRow($pointer = 0)
    {
        $indexes = [];

        ! empty($pointer) ?: $pointer = 0;

        $row = $this->reader->fetchOne($pointer);

        foreach ($row as $column => $field) {
            $pieces = static::split($field);

            foreach ($pieces as $piece) {
                if ($this->validator->validate($piece)) {
                    array_push($indexes, $column);
                }
            }
        }

        return static::deduplicateArray($indexes);
    }

    /**
     * Iterate the reader X times and collect the column index(es) containing identifiers.
     *
     * @param  integer $iterations  The number of rows to iterate. Default: 10.
     * @return array                The columns where an identifier was found.
     */
    public function findIndexes($iterations = 10)
    {
        $indexes = [];

        for ($pointer = 0; $pointer < $iterations; $pointer++) {
            $columns = $this->analyzeRow($pointer);

            foreach ($columns as $column) {
                array_push($indexes, $column);
            }
        }

        return static::deduplicateArray($indexes);
    }

    /**
     * Sort an array, remove duplicate values and remap its keys.
     *
     * @param  array $array
     * @return array
     */
    public static function deduplicateArray($array)
    {
        sort($array);

        return array_values(array_unique($array));
    }
}
