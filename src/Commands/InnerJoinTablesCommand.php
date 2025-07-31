<?php

namespace Irina\PhpDevStack\Commands;

use Irina\PhpDevStack\Classes\CsvDataTable;
use Irina\PhpDevStack\Classes\CsvReader;
use Irina\PhpDevStack\Classes\CsvWriter;
use Irina\PhpDevStack\Dto\CommandParameters;
use Irina\PhpDevStack\Commands\Command;
use Irina\PhpDevStack\Classes\DataTable;
use Irina\PhpDevStack\Services\CsvManipulateTablesService;

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
        if (!($dataTable instanceof CsvDataTable))
            return;

        //$newTable = clone $dataTable;
        $reader = new CsvReader();
        $writer = new CsvWriter();
        $newTable = new CsvDataTable($reader,$writer);
        $newTable->load($this->filePath);

        // print_r($this->columns[0]);
        // print_r($this->columns[1]);
        $service = new CsvManipulateTablesService($dataTable);
        $service->innerJoinTables($newTable, $this->columns[0], $this->columns[1]);
    }
}
