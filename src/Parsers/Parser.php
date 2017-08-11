<?php namespace Dataloader\Parsers;

use League\Csv\Reader;

abstract class Parser
{
    /**
     * @var Reader $reader An instance of the CSV Reader class.
     */
    protected $reader;

    /**
     * Parser constructor.
     *
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * Alternative static constructor.
     *
     * @param  Reader $reader
     * @return Parser
     */
    public static function make(Reader $reader)
    {
        return new static($reader);
    }

    /**
     * Split a string field containing one or multiple identifiers.
     *
     * @param  string $field
     * @return array
     */
    abstract public static function split(string $field);

    /**
     * Analyze a row and find the column index(es) containing identifiers.
     *
     * @param  integer   $pointer   Pointer position in the file.
     * @return array                An array of the matching column indexes.
     */
    public function analyzeRow(int $pointer = 0)
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
     * Iterate the reader X times and collect all column index(es) containing identifiers.
     *
     * @param  integer $iterations  The number of rows to iterate. Default: 10.
     * @return array                The column indexes where an identifier was found.
     */
    public function findIndexes(int $iterations = 10)
    {
        $columnIndexes = [];

        for ($pointer = 0; $pointer < $iterations; $pointer++) {
            $columns = $this->analyzeRow($pointer);

            foreach ($columns as $column) {
                array_push($columnIndexes, $column);
            }
        }

        return static::deduplicateArray($columnIndexes);
    }

    // TODO: change this to one row only. Better for keeping low memory usage.
    public function getIdentifiers(array $columns, int $iterations)
    {
        ! empty($pointer) ?: $pointer = 0;
        ! empty($columns) ?: $columns = [];

        $allIdentifiers = [];

        for ($pointer = 0; $pointer < $iterations ; $pointer++) {
            $row = $this->reader->fetchOne($pointer);

            foreach ($columns as $column) {
                $identifiers = static::split($row[$column]);

                foreach ($identifiers as $identifier) {
                    if ($this->validator->validate($identifier)) {
                        array_push($allIdentifiers, $this->validator->clean($identifier));
                    }
                }
            }
        }

        return $allIdentifiers;
    }

    /**
     * Sort an array, remove duplicate values and remap its keys.
     *
     * @param  array $array
     * @return array
     */
    public static function deduplicateArray(array $array)
    {
        sort($array);

        return array_values(array_unique($array));
    }
}
