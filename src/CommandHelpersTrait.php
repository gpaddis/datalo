<?php namespace Dataloader;

trait CommandHelpersTrait
{
	/**
	 * Check if the number of columns in the heading corresponds to the number
	 * of columns in the first row, and if this number is greater than one.
	 *
	 * @param  League\Csv\Reader $csv
	 * @return boolean
	 */
	public function matchColumnNumbers(\League\Csv\Reader $csv)
	{
		$heading = count($csv->fetchOne(0));
		$firstRow = count($csv->fetchOne(1));
		return ($heading == $firstRow) && $heading > 1;
	}

	/**
	 * Check if the delimiter entered is in the white list.
	 *
	 * @param  string $delimiter
	 * @return boolean | RuntimeException
	 */
	protected function validateDelimiter($delimiter)
	{
		if (! array_key_exists($delimiter, $this->delimiters)) {
			$delimiters = implode('", "', array_keys($this->delimiters));
			throw new \InvalidArgumentException("You entered an invalid delimiter. Try with \"{$delimiters}\".");
		}
	}

	/**
	 * If there's only one column, this means the delimiter is not correct.
	 *
	 * @return void
	 */
	protected function checkForBadDelimiter($csv)
	{
		if (count($csv->fetchOne(1)) <= 1) throw new \RuntimeException("You didn't choose the appropriate delimiter for the file. Try with another one.");
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