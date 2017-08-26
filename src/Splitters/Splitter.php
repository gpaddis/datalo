<?php namespace Dataloader\Splitters;

abstract class Splitter
{
	abstract public function split(string $field) : array;
}
