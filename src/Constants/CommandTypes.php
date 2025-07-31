<?php

namespace Irina\PhpDevStack\Constants;

enum CommandTypes
{
    public const ADD_HEADER = "addheader";
    public const ADD_INDEX_COLUMN = 'indexed';
    public const REORDER_COLUMNS = 'reorder';
    public const REMOVE_COLUMN = 'remove';
    public const TRUNCATE_STRING = 'trunc';
    public const FORMAT_DATE = 'format_date';
    public const MERGE_CSVS = 'merge';
    public const ENCRYPT = 'encrypt';
    public const DECRYPT = 'decrypt';
    public const SIGN_COLUMN = 'sign';
    public const VERIFY_SIGNED_COLUMN = 'verify';
    public const INNER_JOIN = 'join';
    public const SELECT = 'select';
}
