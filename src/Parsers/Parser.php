<?php namespace Dataloader\Parsers;

use League\Csv\Reader;
use Dataloader\Validators\Validator;

abstract class Parser
{
    protected $validator;

    public function __construct(Reader $reader, Validator $validator)
    {
        $this->validator = $validator;
    }

    public static function make(Reader $reader, Validator $validator)
    {
        return new static($validator);
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
    public static function analyzeRow($row, Validator $validator)
    {
        $columns = [];

        if (empty($row)) return [];

        foreach ($row as $column => $value) {
            $splitValues = static::split($value);

            foreach ($splitValues as $identifier) {
                if ($validator->validate($identifier)) {
                    array_push($columns, $column);
                }
            }
        }

        return static::deduplicateArray($columns);
    }

    public static function findColumns(Reader $reader, Validator $validator, $iterations = 10)
    {
        $totalColumns = [];

        for ($pointer = 0; $pointer < $iterations; $pointer++) {
            $row = $reader->fetchOne($pointer);
            $columns = static::analyzeRow($row, $validator);

            foreach ($columns as $column) {
                array_push($totalColumns, $column);
            }
        }

        return static::deduplicateArray($totalColumns);
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
