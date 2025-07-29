<?php

namespace Irina\PhpDevStack\Classes;

abstract class DataTable
{
    protected array $header = [];
    /**
     * @param Row[]
     */
    public function __construct(
        protected array $tableRows = [],
        protected bool $hasHeaders = true,
    ) {}
    public abstract function addRow(array $data = []): void;
    public abstract function addHeader(array $data = []): void;
    public abstract function prependRow(array $data = []): void;
    public abstract function removeRow(int $index): void;

     public abstract function addColumn(string $columnName,int $position):void;
}
