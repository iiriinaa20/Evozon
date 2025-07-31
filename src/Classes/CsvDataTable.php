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
        $this->tableRows[] = new Row($this->normalizeRow($data), $this->header);
    }

    public function addHeader(array $headers = []): void
    {
        $this->header = $headers;
        $this->hasHeaders = true;
    }

    public function updateRow(array $data = [], int $position = 0): void
    {
        $this->tableRows[$position]->setValues($this->normalizeRow($data));
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
        $actual = count($data);
        if($actual > $expected)
            throw new \RuntimeException("Rows headers length missmatch");

        return $actual === $expected
            ? $data
            : array_merge($data, array_fill($actual, $expected - $actual, $fill));
    }

    public function addColumn(string $columnName, int $position = 0, array $data = []): void
    {
        if (!in_array($columnName, $this->header, true)) {
            array_splice($this->header, $position, 0, [$columnName]);
            foreach ($this->tableRows as $idx => $row) {
                $value = $data[$idx] ?? '';
                $values = $row->getValues();
                array_splice($values, $position, 0, [$value]);
                $row->setValues($values);
            }
        }
    }

    public function getRowCount(): int
    {
        return count($this->tableRows);
    }

    public function getHasHeaders(): bool
    {
        return $this->hasHeaders;
    }

    public function getHeader(): array
    {
        return $this->header;
    }

    private function removeColumn(string|int $column): void
    {
        $index = $this->getColumnIndex($column);
        if ($index >= 0 && $index <= count($this->header)) {
            array_splice($this->header, $index, 1);
            foreach ($this->tableRows as $row) {
                $values = $row->getValues();
                array_splice($values, $index, 1);
                $row->setValues($values);
            }
        }
    }

    public function removeColumnByName(string $columnName): void
    {
        $this->removeColumn($columnName);
    }

    public function removeColumnByPosition(int $columnPosition): void
    {
        $this->removeColumn($columnPosition);
    }

    public function getColumnIndex(string|int $column): int
    {
        if (is_string($column)) {
            $index = array_search($column, $this->header, true);
            if ($index === false) {
                return -1;
            }
        } else {
            $index = $column;
            if (!isset($this->header[$index])) {
                return -1;
            }
        }
        return $index;
    }

    public function updateTableColumn(string $columnName, array $newValues, array $positions): void
    {
        if (in_array($columnName, $this->header, true) && !empty($newValues) && !empty($positions)) {
            $columnIndex = $this->getColumnIndex($columnName);
            foreach ($positions as $pos) {
                $row = $this->tableRows[$pos];
                $newRow = $row->getValues();
                $newRow[$columnIndex] = $newValues[$pos];
                $row->setValues($newRow);
            }
        }
    }

    public function reorderColumns(array $newHeaders): void
    {
        $columnIndexes = [];
        foreach ($newHeaders as $header) {
            $index = $this->getColumnIndex($header);
            if ($index === -1) {
                throw new \Exception("Column '$header' does not exist in current header.");
            }
            $columnIndexes[] = $index;
        }

        foreach ($this->tableRows as $row) {
            $oldValues = $row->getValues();
            $newValues = [];
            foreach ($columnIndexes as $index) {
                $newValues[] = $oldValues[$index] ?? '';
            }
            $row->setValues($newValues);
        }

        $this->header = $newHeaders;
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->tableRows);
    }

    public function __clone()
    {
        // Deep copy arrays and clone nested objects to avoid shared state.
        $this->header   = self::deepCopyArray($this->header);
        $this->tableRows = self::deepCopyArray($this->tableRows);
    }

    private static function deepCopyArray(array $arr): array
    {
        $out = [];
        foreach ($arr as $k => $v) {
            if (is_array($v)) {
                $out[$k] = self::deepCopyArray($v);   // recurse
            } elseif (is_object($v)) {
                $out[$k] = clone $v;                  // clone nested objects
            } else {
                // assign by value (no =&), which breaks references on scalars
                $out[$k] = $v;
            }
        }
        return $out;
    }
}
