<?php

namespace Irina\PhpDevStack\Commands;

use Irina\PhpDevStack\Commands\Command;
use Irina\PhpDevStack\Classes\DataTable;
use Irina\PhpDevStack\Dto\CommandParameters;
use Irina\PhpDevStack\Services\CsvEncryptDecryptService;

class DecryptColumnCommand extends Command
{
    //use FormatDateTimeTrait;

    private string $column;
    private string $privkey;

    public function __construct(CommandParameters $params)
    {  
        $this->column = $params->parameters['column'];
        $this->privkey = $params->parameters['privkey'];
    }

    public function run(DataTable $dataTable)
    {   $service = new CsvEncryptDecryptService($dataTable);
        $service->decryptColumnData($this->column, $this->privkey);
    }
}
