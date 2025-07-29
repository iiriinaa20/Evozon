#!/usr/bin/env php
<?php

use Irina\PhpDevStack\Classes\{DataTable, CsvReader, CsvWriter, SecurityManager};
use Irina\PhpDevStack\Classes\CommandRunner;

require_once __DIR__ . '/vendor/autoload.php';

define('CLI_INPUT', 'php://stdin');
define('CLI_OUTPUT', 'php://stdout');

$options = getopt("", [
    "command:",
    "headers:",
    "column:",
    "len:",
    "format:",
    "files:",
    "pubkey:",
    "privkey:",
]);

$commandName = $options["command"] ?? null;

if (!$commandName) {
    echo "Err: Missing --command parameter.\n";
    exit(1);
}

$reader = new CsvReader();
$writer = new CsvWriter(CLI_OUTPUT);
$securityManager = new SecurityManager();
$dataTable = new DataTable($securityManager, $reader, $writer, true);

$skipInput = in_array($commandName, [\Irina\PhpDevStack\Constants\CommandTypes::MERGE_CSVS]);

if (!$skipInput) {
    $dataTable->load(CLI_INPUT);
}

try {
    CommandRunner::run($commandName, $options, $dataTable);
    $writer->setRows($dataTable->rows());
    $dataTable->save(CLI_OUTPUT);
} catch (Throwable $e) {
    echo "Runtime err: " . $e->getMessage() . "\n";
    exit(1);
}
