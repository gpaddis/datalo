<?php namespace Dataloader\Interfaces;

use League\Csv\Reader;

interface ParserInterface
{
	public static function make(Reader $reader);

	public function findIndexes(int $iterations);

	public function extractIdentifiersFromField(array $row, int $column);

	public function collectIdentifiers(int $pointer = 0, array $columns = []);

	public static function split(string $field);
}
