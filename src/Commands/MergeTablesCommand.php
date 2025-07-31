<?php

namespace Irina\PhpDevStack\Commands;

use Irina\PhpDevStack\Classes\CsvDataTable;
use Irina\PhpDevStack\Dto\CommandParameters;
use Irina\PhpDevStack\Commands\Command;
use Irina\PhpDevStack\Classes\DataTable;
use Irina\PhpDevStack\Services\CsvManipulateTablesService;

class MergeTablesCommand extends Command
{
    private array $filePaths;

    public function __construct(CommandParameters $params)
    {
        // print_r($params);
        $this->filePaths = explode(',', $params->parameters['files']);
    }

    public function run(DataTable $dataTable)
    {
        if (!($dataTable instanceof CsvDataTable))
            return;

        $tables = [];
        foreach ($this->filePaths as $file) {

            $newTable = clone $dataTable;
            $newTable->load($file);
            $tables[] = $newTable;
        }

        // var_dump($tables);
        $service = new CsvManipulateTablesService($dataTable);
        $service->mergeFromTables($tables);
    }
}
