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

    public function autodetectDelimiter(\League\Csv\Reader $csv)
    {
        $delimiters = [",", "\t", ";"];

        foreach ($delimiters as $delimiter) {
            $csv->setDelimiter($delimiter);
            $header = $csv->fetchOne(0);
            $firstRow = $csv->fetchOne(1);

            if ($this->matchNumberOfColumns($header, $firstRow)) return $delimiter;
        }

        throw new \RuntimeException("I could not autodetect the correct delimiter. Either the file is corrupted or you can try with a custom delimiter (option --delimiter).");
    }

	/**
	 * Check if the delimiter entered is in the white list.
	 *
	 * @param  string $delimiter
	 * @return boolean | RuntimeException
	 */
	protected function validateDelimiter(string $delimiter)
	{
		if (! array_key_exists($delimiter, $this->delimiters)) {
			$delimiters = implode('", "', array_keys($this->delimiters));
			throw new \InvalidArgumentException("You entered an invalid delimiter. Try with \"{$delimiters}\".");
		}
	}

	/**
	 * Check if the delimiter set to the Reader isntance is wrong.
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
	protected function isEmpty($file) : bool
	{
		return filesize($file) == 0;
	}

	protected function verifyDestinationDoesntExist($destination)
	{
		if (file_exists($destination)) throw new \RuntimeException('Destination file already exists. Please choose another file name or use the --force option to overwrite it.');
	}

	/**
	 * Count the number of lines in a file.
	 *
	 * @param  string $file
	 * @return integer
	 */
	protected function countLines($file)
	{
		$lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		return count($lines);
	}
}
