<?php

namespace Irina\PhpDevStack\Commands;

use Irina\PhpDevStack\Classes\DataTable;
use Irina\PhpDevStack\Commands\Command;
use Irina\PhpDevStack\Dto\CommandParameters;

class AddHeaderCommand extends Command
{
    private array $headers = [];

    public function __construct(CommandParameters $params)
    {
        $this->headers = explode(",", $params->parameters['headers']);
    }

    public function run(DataTable $dataTable)
    {
        $dataTable->prependRow($this->headers);
    }
}
