#!/usr/bin/env php
<?php

use Irina\PhpDevStack\Classes\{CsvDataTable, DataTable, CsvReader, CsvWriter, Row, SecurityManager};
use Irina\PhpDevStack\Classes\CommandRunner;
use Irina\PhpDevStack\Services\CsvEncryptDecryptService;

require_once __DIR__ . '/vendor/autoload.php';

define('CLI_INPUT', 'php://stdin');
define('CLI_OUTPUT', 'php://stdout');

$a = new DateTime();
// var_dump(gettype($a));
// exit();
$reader = new CsvReader();
$writer = new CsvWriter();
$p = new CsvDataTable($reader,$writer,true);
// $p = new CsvDataTable($reader,$writer,false);
// $p->load(CLI_INPUT);

// $p->save(CLI_OUTPUT);
//var_dump($p);


//$row = new Row();
//$row->setHeader(['name','age']);
//$row->setValues(['BOB',44]);
//$row->setValues(['ana',12]);
//$row->setValues(['mara',8]);
//$p->addRow([1,2,5,5]);
//$p->addRow([3,4]);
//$/p->addRow([5,6]);

//$p->removeColumnByName('age');
//$p->save(CLI_OUTPUT);
//print_r($row->getRowWithHeaders());

//exit(1);

$options = getopt("", [
    "command:", "headers:", "column:", "len:", "format:",
    "op:", "value:", "get:",
    "files:", "pubkey:", "privkey:",
]);

$commandName = $options["command"] ?? null;

// var_dump($options);
// exit(0);
if (!$commandName) {
    echo "Err: Missing --command parameter.\n";
    exit(1);
}

$reader = new CsvReader();
$writer = new CsvWriter(CLI_OUTPUT);
$securityManager = new SecurityManager();
// $dataTable = new DataTable($securityManager, $reader, $writer, true);

$skipInput = in_array($commandName, [\Irina\PhpDevStack\Constants\CommandTypes::MERGE_CSVS]);

if (!$skipInput) {
    $p->load(CLI_INPUT);
}

try {
    CommandRunner::run($commandName, $options, $p);
    // $writer->setRows($p->rows());
//$p->updateTableColumn('age',[],[1]);
//$service = new CsvEncryptDecryptService($p);
//$service->encryptColumnData('age','public.pem');  

$p->save(CLI_OUTPUT);

} catch (Throwable $e) {
    echo "Runtime err: " . $e->getMessage() . "\n";
    exit(1);
}
