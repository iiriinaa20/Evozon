#!/usr/bin/env php
<?php

define('CLI_INPUT', 'php://stdin');
define('CLI_OUTPUT', 'php://stdout');

require_once __DIR__ . '/vendor/autoload.php';

$commands = [
    'addHeader' => [
        "namespace" => Irina\PhpDevStack\Commands\AddHeader::class,
        "params" => [
            'headers'
        ]
    ],
];

$options = getopt("", ["command:", "headers:"]);
$commandName = $options["command"] ?? null;

$commandDetails = $commands[$commandName] ?? null;
if (!$commandDetails) {
    echo "comanda gresita sau inexistenta";
    exit;
}

$commandRequiredParameters = $commandDetails["params"];
$commandParameters = [];
foreach ($commandRequiredParameters as $value) {
    $commandParameters[$value] = $options[$value] ?? null;
}
// print_r($options);
// print_r($commandDetails);
// print_r($commandParameters);
// exit;
$command = new $commandDetails["namespace"]();
$command->run($commandParameters);
