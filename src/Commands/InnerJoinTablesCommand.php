<?php

namespace Irina\PhpDevStack\Commands;

use Irina\PhpDevStack\Dto\CommandParameters;
use Irina\PhpDevStack\Commands\Command;
use Irina\PhpDevStack\Classes\DataTable;

class InnerJoinTablesCommand extends Command
{
    private string $filePath;
    private array $columns;

    public function __construct(CommandParameters $params)
    {
        $this->filePath =  $params->parameters['files'];
        $this->columns = explode(',', $params->parameters['column']);
    }

    public function run(DataTable $dataTable)
    {
        $newTable = clone $dataTable;
        $newTable->load($this->filePath);

        // print_r($this->columns[0]);
        // print_r($this->columns[1]);
        $dataTable->innerJoin($newTable, $this->columns[0], $this->columns[1]);
    }
}
