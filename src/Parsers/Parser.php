<?php

namespace Dataloader\Parsers;

abstract class Parser
{
    /**
     * @var Dataloader\Splitters\Splitter
     * @var Dataloader\Validators\Validator $validator
     */
    protected $splitter;
    protected $validator;

    /**
     * Alternative static constructor.
     *
     * @return Parser
     */
    public static function make() : Parser
    {
        return new static();
    }

    /**
     * Analyze a row and return the column index(es) containing identifiers.
     *
     * @param array $row
     *
     * @return array
     */
    public function analyzeRow(array $row = []) : array
    {
        $indexes = [];

        foreach ($row as $columnNumber => $field) {
            if ($this->containsIdentifiers($field)) {
                $indexes[] = $columnNumber;
            }
        }

        return $indexes;
    }

    /**
     * Check if a field contains identifiers.
     *
     * @param string $field
     *
     * @return bool
     */
    protected function containsIdentifiers(string $field) : bool
    {
        $candidates = $this->split($field);

        foreach ($candidates as $candidate) {
            if ($this->validate($candidate)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Collect all column index(es) containing identifiers in the given rows.
     *
     * @param array $rows
     *
     * @return array
     */
    public function findAllIndexes(array $rows = []) : array
    {
        $result = [];

        foreach ($rows as $row) {
            $columns = $this->analyzeRow($row);

            $result = array_merge($columns, $result);
        }

        return static::deduplicateArray($result);
    }

    /**
     * Extract all valid identifiers contained in a single field.
     *
     * @param array $row
     * @param int   $column
     *
     * @return array
     */
    public function extractIdentifiersFromField(array $row, int $column) : array
    {
        $result = [];

        $candidates = $this->split($row[$column]);

        foreach ($candidates as $candidate) {
            if ($this->validate($candidate)) {
                $result[] = $this->clean($candidate);
            }
        }

        return $result;
    }

    /**
     * Collect all unique identifiers from a single row checking only in the columns specified
     * and return them in a sorted array.
     *
     * @param array $row     The row to analyze.
     * @param array $columns The columns where it should look for identifiers.
     *
     * @return array
     */
    public function collectIdentifiers(array $row = [], array $columns = []) : array
    {
        $result = [];

        foreach ($columns as $column) {
            if ($this->exists($column, $row)) {
                $identifiers = $this->extractIdentifiersFromField($row, $column);

                $result = array_merge($identifiers, $result);
            }
        }

        return array_unique($result);
    }

    /**
     * Check whether a column exists in the row passed.
     *
     * @param int   $column
     * @param array $row
     *
     * @return bool
     */
    protected function exists(int $column, array $row) : bool
    {
        return array_key_exists($column, $row);
    }

    /**
     * Use the split() function of the splitter passed in the constructor.
     *
     * @param string $field
     *
     * @return array
     */
    protected function split(string $field) : array
    {
        return $this->splitter->split($field);
    }

    /**
     * Use the validate() function of the validator passed in the constructor.
     *
     * @param string $identifier
     *
     * @return bool
     */
    protected function validate(string $identifier) : bool
    {
        return $this->validator->validate($identifier);
    }

    /**
     * Use the clean() function of the validator passed in the constructor.
     *
     * @param string $identifier
     *
     * @return string
     */
    protected function clean(string $identifier) : string
    {
        return $this->validator->clean($identifier);
    }

    /**
     * Sort an array, remove duplicate values and remap its keys.
     *
     * @param array $array
     *
     * @return array
     */
    public static function deduplicateArray(array $array) : array
    {
        sort($array);

        return array_values(array_unique($array));
    }
}
