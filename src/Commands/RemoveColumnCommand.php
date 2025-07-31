<?php

namespace Irina\PhpDevStack\Commands;

use Irina\PhpDevStack\Classes\DataTable;
use Irina\PhpDevStack\Commands\Command;
use Irina\PhpDevStack\Dto\CommandParameters;

class RemoveColumnCommand extends Command
{
    private string|int $column;

    public function __construct(CommandParameters $params)
    {
        $this->column = $params->parameters['column'];

        if (is_numeric($params->parameters['column']))
            $this->column = intval($params->parameters['column']);
    }

    public function run(DataTable $dataTable)
    {
        if (is_string($this->column))
            $dataTable->removeColumnByName($this->column);
        else
            $dataTable->removeColumnByPosition($this->column);
    }
}
