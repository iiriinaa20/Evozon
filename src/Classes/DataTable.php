<?php

namespace Irina\PhpDevStack\Classes;

abstract class DataTable
{
    protected array $header = [];
    /**
     * @param Row[] $tableRows   
     * @param bool  $hasHeaders  
     */
    public function __construct(
        protected array $tableRows = [],
        protected bool $hasHeaders = true,
    ) {}
    public abstract function addRow(array $data = []): void;
    public abstract function addHeader(array $data = []): void;
    public abstract function prependRow(array $data = []): void;
    public abstract function removeRow(int $index): void;

    public abstract function addColumn(string $columnName, int $position): void;
    public abstract function removeColumnByName(string $columnName): void;
    public abstract function removeColumnByPosition(int $columnPosition): void;
    public abstract function updateTableColumn(string $columnName, array $newValues, array $positions): void;
    public abstract  function reorderColumns(array $newHeaders): void;
}
