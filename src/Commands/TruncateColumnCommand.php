<?php

namespace Irina\PhpDevStack\Commands;

use Irina\PhpDevStack\Classes\DataTable;
use Irina\PhpDevStack\Commands\Command;
use Irina\PhpDevStack\Dto\CommandParameters;

class TruncateColumnCommand extends Command
{
    private string $column;
    private int $len;

    public function __construct(CommandParameters $params)
    {
        // print_r($params);
        $this->column = $params->parameters['column'];
        $this->len = $params->parameters['len'];
    }

    public function run(DataTable $dataTable)
    {
        $dataTable->truncateColumn($this->column, $this->len);
    }
}
