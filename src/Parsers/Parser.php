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

    /**
     * Fetch all valid identifiers from a single row checking only in the columns specified.
     * 
     * @param  array  $columns The columns where there it should look for identifiers.
     * @param  int    $pointer The row number to analyze.
     * @return array           The valid identifiers found.
     */
    public function getIdentifiers(array $columns = [], int $pointer = 0)
    {
        // ! empty($pointer) ?: $pointer = 0;

        $identifiersFound = [];

        $row = $this->reader->fetchOne($pointer);

        foreach ($columns as $column) {
            // Avoid an undefined offset error.
            if ($column >= $this->countColumns()) {
                continue;
            }

            $identifiers = static::split($row[$column]);

            foreach ($identifiers as $identifier) {
                if ($this->validator->validate($identifier)) {
                    array_push($identifiersFound, $this->validator->clean($identifier));
                }
            }
        }

        return $identifiersFound;
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

    /**
     * Count the columns in the csv file passed with the Reader instance.
     * 
     * @return integer Number of columns in the first row.
     */
    protected function countColumns()
    {
        $firstRow = $this->reader->fetchOne(0);
        return count($firstRow);
    }
}
