<?php namespace Dataloader\Parsers;

use Dataloader\Splitters\IsbnSplitter;
use Dataloader\Validators\IsbnValidator;

class IsbnParser extends Parser
{
    /**
     * IsbnParser Constructor.
     */
    public function __construct()
    {
        $this->validator = new IsbnValidator;
        $this->splitter = new IsbnSplitter;
    }
}
