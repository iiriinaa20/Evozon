<?php

namespace Irina\PhpDevStack\Classes;

use Irina\PhpDevStack\Contracts\FileReaderInterface;

class CsvDataTable extends DataTable
{
    public function __construct(
        private CsvReader $csvReader,
        private CsvWriter $csvWriter,
        bool $hasHeader = true,
    ) {
        parent::__construct(hasHeaders: $hasHeader);
    }

    public function load(string $path): void
    {
        $rows = $this->csvReader->read($path);
        if ($this->hasHeaders) {
            $this->header = array_shift($rows);
        }
        $this->tableRows = array_map(fn($row) => new Row($row, $this->header), $rows);
    }

    public function save(string $path): void
    {
        $this->csvWriter->setRows($this->tableRows);
        $this->csvWriter->write($path);
    }

    public function addRow(array $data = []): void
    {
        $this->tableRows[] = new Row($data, $this->header);
    }

    public function addHeader(array $headers = []): void
    {
        $this->header = $headers;
        $this->hasHeaders = true;
    }

    public function prependRow(array $data = []): void
    {
        $data = $this->normalizeRow($data);
        array_unshift($this->tableRows, new Row($data, $this->header));
    }

    public function removeRow(int $index): void
    {

        if (isset($this->tableRows[$index])) {
            array_splice($this->tableRows, $index, 1);
        }

    }

    private function normalizeRow(array $data, mixed $fill = ''): array
    {
        $expected = count($this->header ?? []);
        return $data ?: array_fill(0, $expected, $fill);
    }

    public function addColumn(string $columnName,int $position):void{
        

    }
}
