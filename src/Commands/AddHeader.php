<?php

namespace Irina\PhpDevStack\Commands;

use Irina\PhpDevStack\Classes\CsvHandler;
use Irina\PhpDevStack\Commands\Command;

class AddHeader extends Command
{
    private CsvHandler $csvHandler;

    public function __construct()
    {
        $this->csvHandler = CsvHandler::fromFile(CLI_INPUT);
    }

    public function run($args)
    {
        // print_r($args);
        $this->csvHandler->readFile();

        $headers = $args["headers"];
        $headers = explode(",", $headers);

        $rows = &$this->csvHandler->getRows();
        array_unshift($rows, $headers);
        $this->csvHandler->writeFile(CLI_OUTPUT);
    }
}
