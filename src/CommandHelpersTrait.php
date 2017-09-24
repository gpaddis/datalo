<?php

namespace Dataloader;

trait CommandHelpersTrait
{
    /**
     * Count the number of lines in a file.
     *
     * @param string $file
     *
     * @return int
     */
    protected function countLines(string $file) : int
    {
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        return count($lines);
    }

    /**
     * Find all columns containing identifiers in the number of lines specified.
     *
     * @param League\Csv\Reader $csv
     * @param int               $lines
     *
     * @return array
     */
    protected function findIdentifierColumns($csv, $lines = 25)
    {
        $rows = $csv->setOffset(1)->setLimit($lines)->fetchAll();
        if (!$indexes = $this->parser->findAllIndexes($rows)) {
            throw new \RuntimeException('No identifiers found in the source file.');
        }

        return $indexes;
    }

    /**
     * Autodetect the delimiter of the file passed.
     *
     * Count all occurrences of a delimiter in the first and second row of the file
     * specified: if the count matches return the delimiter,
     * otherwise throw an exception.
     *
     * @param string $filename
     *
     * @throws RuntimeException
     *
     * @return string
     */
    public function autodetectDelimiter(string $filename)
    {
        $this->validateFile($filename);

        $content = file($filename);

        foreach ($this->delimiters as $delimiter) {
            if ($this->matchDelimiterCount($content[0], $content[1], $delimiter)) {
                return $delimiter;
            }
        }

        throw new \RuntimeException('Unable to autodetect the correct delimiter. Either the file is corrupted or you can try with a custom delimiter (option --delimiter).');
    }

    /**
     * Match the number of occurrences of a delimiter across two rows.
     *
     * @param string $row1
     * @param string $row2
     * @param string $delimiter
     *
     * @return bool
     */
    public function matchDelimiterCount(string $row1, string $row2, string $delimiter) : bool
    {
        $count1 = substr_count($row1, $delimiter);
        $count2 = substr_count($row2, $delimiter);

        return $count1 == $count2 && $count1 > 0;
    }

    /**
     * Check if the file exist or has some content.
     *
     * @param string $filename
     */
    public function validateFile(string $filename)
    {
        if (!file_exists($filename) || filesize($filename) == 0) {
            throw new \RuntimeException('You are trying to open an invalid file. Try with another one.');
        }
    }

    /**
     * Verify if the destination file does not exist.
     *
     * @param string $destination
     *
     * @return void
     */
    protected function verifyDestinationDoesntExist(string $destination)
    {
        if (file_exists($destination)) {
            throw new \RuntimeException('Destination file already exists. Please choose another file name or use the --force option to overwrite it.');
        }
    }
}
