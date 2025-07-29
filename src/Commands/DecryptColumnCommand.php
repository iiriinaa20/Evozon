<?php

namespace Irina\PhpDevStack\Commands;

use Irina\PhpDevStack\Commands\Command;
use Irina\PhpDevStack\Classes\DataTable;
use Irina\PhpDevStack\Dto\CommandParameters;

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
    {
        $dataTable->decryptColumn($this->column, $this->privkey);
    }
}
