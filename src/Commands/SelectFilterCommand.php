<?php

namespace Irina\PhpDevStack\Commands;

use Irina\PhpDevStack\Classes\CsvDataTable;
use Irina\PhpDevStack\Classes\CsvReader;
use Irina\PhpDevStack\Classes\CsvWriter;
use Irina\PhpDevStack\Dto\CommandParameters;
use Irina\PhpDevStack\Commands\Command;
use Irina\PhpDevStack\Classes\DataTable;
use Irina\PhpDevStack\Services\CsvManipulateTablesService;

class SelectFilterCommand extends Command
{

    public function __construct(private CommandParameters $params) {}

    // public function handleQuery(array $params, array $headers, array $rows): array
    // {
    //     $column = $params['column'] ?? null;
    //     $op     = $params['op'] ?? '=';
    //     $value  = $params['value'] ?? null;
    //     $select = $params['get'] ?? null;
    public function run(DataTable $dataTable)
    {
        if (!($dataTable instanceof CsvDataTable))
            return;

        $headers = $dataTable->getHeader();
        $rows = array_map(fn($r) => $r->getValues(), iterator_to_array($dataTable->getIterator()));
        $service = new CsvManipulateTablesService($dataTable);
        // var_dump($this);
        $service->handleQuery($this->params->parameters, $headers, $rows);
        // var_dump($filtered);

        // $reader = new CsvReader();
        // $writer = new CsvWriter();

        // $newTable = new CsvDataTable($reader, $writer);
        // $newTable->addHeader(array_keys($filtered[0] ?? []));
        // foreach ($filtered as $key => $value) {
        //     $newTable->addRow($value);
        // }
        // $newTable->save(CLI_OUTPUT);

    }
}
