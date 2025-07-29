<?php

namespace Irina\PhpDevStack\Dto;

use Irina\PhpDevStack\Constants\CommandOptions;
use Irina\PhpDevStack\Constants\CommandTypes;

class CommandData
{

    public static function getBindings(): array
    {

        return [
            CommandTypes::ADD_HEADER => [
                CommandOptions::HEADERS
            ],
            CommandTypes::ADD_INDEX_COLUMN => [
                CommandOptions::COLUMN
            ],
            CommandTypes::REORDER_COLUMNS => [
                CommandOptions::HEADERS
            ],
            CommandTypes::REMOVE_COLUMN => [
                CommandOptions::COLUMN
            ],
            CommandTypes::TRUNCATE_STRING => [
                CommandOptions::COLUMN,
                CommandOptions::LEN,
            ],
            CommandTypes::FORMAT_DATE => [
                CommandOptions::COLUMN,
                CommandOptions::FORMAT,
            ],
            CommandTypes::MERGE_CSVS => [
                CommandOptions::FILES,
            ],
            CommandTypes::ENCRYPT => [
                CommandOptions::COLUMN,
                CommandOptions::PUBLIC_KEY

            ],
            CommandTypes::DECRYPT => [
                CommandOptions::COLUMN,
                CommandOptions::PRIVATE_KEY

            ],
             CommandTypes::SIGN_COLUMN => [
                CommandOptions::COLUMN,
                CommandOptions::PRIVATE_KEY

            ],
            
             CommandTypes::VERIFY_SIGNED_COLUMN => [
                CommandOptions::COLUMN,
                CommandOptions::PUBLIC_KEY
            ],

             CommandTypes::INNER_JOIN => [
                CommandOptions::COLUMN,
                CommandOptions::FILES
             ]
        ];
    }

    public static function getOptionsFor(string $commandType): array
    {
        return self::getBindings()[$commandType] ?? [];
    }
}
