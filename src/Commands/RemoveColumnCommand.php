<?php

namespace Irina\PhpDevStack\Commands;

use Irina\PhpDevStack\Classes\DataTable;
use Irina\PhpDevStack\Commands\Command;
use Irina\PhpDevStack\Dto\CommandParameters;

class RemoveColumnCommand extends Command
{
    private string $column;

    public function __construct(CommandParameters $params)
    {
       
        $this->column = $params->parameters['column'];
    }

    public function run(DataTable $dataTable)
    {
        $dataTable->removeColumn($this->column);
    }
}
