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
}