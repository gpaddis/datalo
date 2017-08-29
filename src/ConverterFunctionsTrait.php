<?php namespace Dataloader;

trait ConverterFunctionsTrait
{
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
		if (count($csv->fetchOne(1)) <= 1) throw new \RuntimeException("You didn't choose the appropriate delimiter. Try with another one.");
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
		if (file_exists($destination)) throw new \RuntimeException('Destination file already exists. Please choose another file name.');
	}

	/**
	 * Remove duplicate rows from the given source file.
	 * If no destination file is passed, it saves the content in the source file.
	 * The method returns the number of lines saved in the destination file.
	 *
	 * @param  string $source
	 * @param  string $destination
	 * @return integer
	 */
	protected function removeDuplicates($source, $destination = '')
	{
		$destination = $destination ?: $source;

		$lines = file($source, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		$lines = array_unique($lines);
		file_put_contents($destination, implode(PHP_EOL, $lines));

		return count($lines);
	}

	protected function countLines($file)
	{
		$lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		return count($lines);
	}
}
