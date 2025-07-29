<?php

namespace Irina\PhpDevStack\Commands;

use Irina\PhpDevStack\Commands\Command;
use Irina\PhpDevStack\Classes\DataTable;
use Irina\PhpDevStack\Dto\CommandParameters;

class EncryptColumnCommand extends Command
{
    //use FormatDateTimeTrait;

    private string $column;
    private string $pubkey;

    public function __construct(CommandParameters $params)
    {
        $this->column = $params->parameters['column'];
        $this->pubkey = $params->parameters['pubkey'];
    }

    public function run(DataTable $dataTable)
    {
        $dataTable->encryptColumn($this->column, $this->pubkey);
    }
}
