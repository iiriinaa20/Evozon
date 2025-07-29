<?php

namespace Irina\PhpDevStack\Commands;

use Irina\PhpDevStack\Classes\DataTable;

abstract class Command
{
    public abstract function run(DataTable $dataTable);
}
