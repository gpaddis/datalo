<?php

namespace Dataloader\Parsers;

use Dataloader\Splitters\Splitter;
use Dataloader\Validators\IssnValidator;

class IssnParser extends Parser
{
    /**
     * IssnParser Constructor.
     */
    public function __construct()
    {
        $this->validator = new IssnValidator();
        $this->splitter = new Splitter();
    }
}
