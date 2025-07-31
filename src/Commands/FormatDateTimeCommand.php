<?php

namespace Irina\PhpDevStack\Commands;

use Irina\PhpDevStack\Commands\Command;
use Irina\PhpDevStack\Classes\DataTable;
use Irina\PhpDevStack\Dto\CommandParameters;
use Irina\PhpDevStack\Services\CsvColumnModifierService;

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
        $service = new CsvColumnModifierService($dataTable);
        $service->formatDateFromColumn($this->column, $this->format);
    }
}
