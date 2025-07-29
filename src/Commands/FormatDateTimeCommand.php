<?php

namespace Irina\PhpDevStack\Commands;

use Irina\PhpDevStack\Commands\Command;
use Irina\PhpDevStack\Classes\DataTable;
use Irina\PhpDevStack\Dto\CommandParameters;

class FormatDateTimeCommand extends Command
{
    //use FormatDateTimeTrait;

    private string $column;
    private string $format;

    public function __construct(CommandParameters $params)
    {
        $this->column = $params->parameters['column'];
        $this->format = $params->parameters['format'];
    }

    public function run(DataTable $dataTable)
    {
        $dataTable->formatDateTime($this->column, $this->format);
    }
}
