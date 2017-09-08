<?php namespace Dataloader;

trait CommandHelpersTrait
{
    /**
     * Check if the number of columns in the header corresponds to the number of
     * columns in the first row, and if the header contains more than one column.
     *
     * @param  League\Csv\Reader $csv
     * @return boolean
     */
    public function matchNumberOfColumns(array $header, array $firstRow) : bool
    {
        $headerColumns = count($header);
        $firstRowColumns = count($firstRow);
        return ($headerColumns == $firstRowColumns) && $headerColumns > 1;
    }

    /**
     * Autodetect the delimiter in the csv file passed.
     *
     * @param  \League\Csv\Reader $csv
     * @return string
     */
    public function autodetectDelimiter(\League\Csv\Reader $csv) : string
    {
        $delimiters = [",", "\t", ";"];

        foreach ($delimiters as $delimiter) {
            $csv->setDelimiter($delimiter);
            $header = $csv->fetchOne(0);
            $firstRow = $csv->fetchOne(1);

            if ($this->matchNumberOfColumns($header, $firstRow)) {
                return $delimiter;
            }
        }

        throw new \RuntimeException("Unable to autodetect the correct delimiter. Either the file is corrupted or you can try with a custom delimiter (option --delimiter).");
    }

    /**
     * Check whether the delimiter set in the Reader instance is wrong.
     *
     * @return void
     */
    protected function checkForBadDelimiter(\League\Csv\Reader $csv)
    {
        $header = $csv->fetchOne(0);
        $firstRow = $csv->fetchOne(1);

        if (! $this->matchNumberOfColumns($header, $firstRow)) {
            throw new \RuntimeException("You didn't choose the appropriate delimiter for the file. Try with another one.");
        }
    }

    /**
     * Return true if the file is empty.
     *
     * @param  string  $file
     * @return boolean
     */
    protected function isEmpty(string $file) : bool
    {
        return filesize($file) == 0;
    }

    /**
     * Verify if the destination file does not exist.
     *
     * @param  string $destination
     * @return void
     */
    protected function verifyDestinationDoesntExist(string $destination)
    {
        if (file_exists($destination)) {
            throw new \RuntimeException('Destination file already exists. Please choose another file name or use the --force option to overwrite it.');
        }
    }

    /**
     * Count the number of lines in a file.
     *
     * @param  string $file
     * @return integer
     */
    protected function countLines(string $file) : int
    {
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        return count($lines);
    }

    /**
     * Find all columns containing identifiers in the number of lines specified.
     *
     * @param  League\Csv\Reader $csv
     * @param  int $lines
     * @return array
     */
    protected function findIdentifierColumns($csv, $lines = 25)
    {
        $rows = $csv->setOffset(1)->setLimit($lines)->fetchAll();
        if (! $indexes = $this->parser->findAllIndexes($rows)) {
            throw new \RuntimeException("No identifiers found in the source file.");
        }

        return $indexes;
    }

    /**
     * Count the occurrences of a delimiter in the first and second row of the
     * file specified: if the count matches for a delimiter return the delimiter,
     * otherwise throw an exception.
     * 
     * @param  string $filename
     *
     * @throws RuntimeException
     * 
     * @return string
     */
    public function matchDelimiterCount(string $filename)
    {
        $delimiters = [",", "\t", ";"];
        $content = file($filename);

        foreach ($delimiters as $delimiter) {
            $count1 = substr_count($content[0], $delimiter);
            $count2 = substr_count($content[1], $delimiter);

            if ($count1 == $count2 && $count1 > 0) {
                return $delimiter;
            }
        }

        throw new \RuntimeException("Unable to autodetect the correct delimiter. Either the file is corrupted or you can try with a custom delimiter (option --delimiter).");
    }
}
