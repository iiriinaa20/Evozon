<?php

namespace Irina\PhpDevStack\Classes;

use Irina\PhpDevStack\Classes\DataTable;
use Irina\PhpDevStack\Dto\CommandParameters;
use Irina\PhpDevStack\Constants\CommandTypes;

class CommandRunner
{
    public static function run(string $commandName, array $options, DataTable $dataTable): void
    {
        $params = self::buildParameters($options, $commandName);

        switch ($commandName) {
            case CommandTypes::ADD_HEADER:
                $cmd = new \Irina\PhpDevStack\Commands\AddHeaderCommand($params);
                break;
            case CommandTypes::ADD_INDEX_COLUMN:
                $cmd = new \Irina\PhpDevStack\Commands\AddIndexColumnCommand($params);
                break;
            case CommandTypes::REMOVE_COLUMN:
                $cmd = new \Irina\PhpDevStack\Commands\RemoveColumnCommand($params);
                break;
            case CommandTypes::REORDER_COLUMNS:
                $cmd = new \Irina\PhpDevStack\Commands\ReorderColumnsCommand($params);
                break;
            case CommandTypes::TRUNCATE_STRING:
                $cmd = new \Irina\PhpDevStack\Commands\TruncateColumnCommand($params);
                break;
            case CommandTypes::FORMAT_DATE:
                $cmd = new \Irina\PhpDevStack\Commands\FormatDateTimeCommand($params);
                break;
            case CommandTypes::MERGE_CSVS:
                $cmd = new \Irina\PhpDevStack\Commands\MergeTablesCommand($params);
                break;
            case CommandTypes::ENCRYPT:
                $cmd = new \Irina\PhpDevStack\Commands\EncryptColumnCommand($params);
                break;
            case CommandTypes::DECRYPT:
                $cmd = new \Irina\PhpDevStack\Commands\DecryptColumnCommand($params);
                break;
            case CommandTypes::SIGN_COLUMN:
                $cmd = new \Irina\PhpDevStack\Commands\SignColumnCommand($params);
                break;
            case CommandTypes::VERIFY_SIGNED_COLUMN:
                $cmd = new \Irina\PhpDevStack\Commands\VerifySignedColumnCommand($params);
                break;
            case CommandTypes::INNER_JOIN:
                $cmd = new \Irina\PhpDevStack\Commands\InnerJoinTablesCommand($params);
                break;
            default:
                throw new \RuntimeException("Unknown command: {$commandName}");
        }

        $cmd->run($dataTable);
    }

    private static function buildParameters(array $options, string $commandName): CommandParameters
    {
        $availableOptions = \Irina\PhpDevStack\Dto\CommandData::getOptionsFor($commandName);
        $parameters = [];

        foreach ($availableOptions as $optionKey) {
            if (!isset($options[$optionKey])) {
                throw new \InvalidArgumentException("Missing option --{$optionKey} for command {$commandName}");
            }
            $parameters[$optionKey] = $options[$optionKey];
        }

        return new CommandParameters($parameters);
    }
}
