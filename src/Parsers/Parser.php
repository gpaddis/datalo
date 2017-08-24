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
     * Analyze a row and find the column index(es) containing identifiers.
     *
     * @param  integer   $pointer   Pointer position in the file.
     * @return array                An array of the matching column indexes.
     */
    public function analyzeRow(int $pointer = 0)
    {
        $indexes = [];

        $row = $this->reader->fetchOne($pointer);

        foreach ($row as $column => $field) {
            if ($this->containsIdentifiers($field)) {
                array_push($indexes, $column);
            }
        }

        return $indexes;
    }

    /**
     * Check if a field contains identifiers.
     * 
     * @param  string  $field
     * @return boolean
     */
    protected function containsIdentifiers($field)
    {
        $candidates = static::split($field);

        foreach ($candidates as $candidate) {
            if ($this->validator->validate($candidate)) return true;
        }

        return false;
    }

    /**
     * Iterate the reader X times and collect all column index(es) containing identifiers.
     *
     * @param  integer $iterations  The number of rows to iterate. Default: 10.
     * @return array                The column indexes where an identifier was found.
     */
    public function findIndexes(int $iterations = 10)
    {
        $result = [];

        for ($pointer = 0; $pointer < $iterations; $pointer++) {
            $columns = $this->analyzeRow($pointer);

            $result = array_merge($columns, $result);
        }

        return static::deduplicateArray($result);
    }

    /**
     * Extract all valid identifiers contained in a single field.
     * 
     * @param  array    $row
     * @param  integer  $column
     * @return array
     */
    public function extractIdentifiersFromField(array $row, int $column)
    {
        $result = [];

        $candidates = static::split($row[$column]);
        
        foreach ($candidates as $candidate) {
            if ($this->validator->validate($candidate)) {
                array_push($result, $this->validator->clean($candidate));
            }
        }

        return $result;
    }

    /**
     * Collect all identifiers from a single row checking only in the columns specified.
     * 
     * @param  integer  $pointer  The row number to analyze.
     * @param  array    $columns  The columns where it should look for identifiers.
     * @return array              The valid identifiers found.
     */
    public function collectIdentifiers(int $pointer = 0, array $columns = [])
    {
        $result = [];

        $row = $this->reader->fetchOne($pointer);

        foreach ($columns as $column) { 
            if ($this->exists($column)){
                $identifiers = $this->extractIdentifiersFromField($row, $column);

                $result = array_merge($identifiers, $result);
            }
        }
        return $result;
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
     * Check whether a column exists in the current Reader instance.
     * 
     * @param  int  $column
     * @return boolean
     */
    protected function exists(int $column)
    {
        $firstRow = $this->reader->fetchOne(0);
        
        return array_key_exists($column, $firstRow);
    }

    /**
     * Split a string field containing one or multiple identifiers.
     *
     * @param  string $field
     * @return array
     */
    abstract public static function split(string $field);
}
