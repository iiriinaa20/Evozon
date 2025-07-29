<?php

namespace Irina\PhpDevStack\Classes;

use Carbon\Carbon;
use Irina\PhpDevStack\Contracts\FileReaderInterface;
use Irina\PhpDevStack\Contracts\FileWriterInterface;
use InvalidArgumentException;
use Irina\PhpDevStack\Traits\TruncateStringTrait;
use OutOfRangeException;
use RuntimeException;

class DataTable
{
    private array $rows = [];
    use TruncateStringTrait;
    public function __construct(
        private SecurityManager $securityManager,
        private FileReaderInterface $reader,
        private FileWriterInterface $writer,
        private bool $hasHeader = true,
    ) {}

    public function load(string $path): void
    {
        $this->rows = $this->reader->read($path);
    }

    public function save(string $path): void
    {
        $this->writer->write($path, $this->rows());
    }

    public function addRow(array $data = []): void
    {
        $data = $this->normalizeRow($data);
        $this->rows[] = new Row($data);
    }

    public function prependRow(array $data = []): void
    {
        if ($this->hasHeader) {
            return;
        }

        $data = $this->normalizeRow($data);
        array_unshift($this->rows(), new Row($data));
    }

    public function removeRow(int $index): void
    {
        if (!isset($this->rows()[$index])) {
            throw new OutOfRangeException("Row {$index} not found");
        }

        unset($this->rows()[$index]);
        $this->rows = array_values($this->rows());
    }

    public function addIndexedColumn(string $name, int $position = 0): void
    {
        foreach ($this->rows() as $rowIdx => $row) {
            $data  = $row->getRow();
            $value = $rowIdx === 0 ? $name : $rowIdx;
            array_splice($data, $position, 0, $value);
            $row->setRow($data);
        }
    }

    public function addColumn(string $name, int $position = 0): void
    {
        foreach ($this->rows() as $rowIdx => $row) {
            $data  = $row->getRow();
            $value = $rowIdx === 0 ? $name : $rowIdx;
            array_splice($data, $position, 0, $value);
            $row->setRow($data);
        }
    }

    public function removeColumn(string|int $column): void
    {
        $index = is_int($column)
            ? $column
            : array_search($column, $this->getHeaderRow()->getRow(), true);

        if ($index === false) {
            throw new InvalidArgumentException("Column {$column} not found");
        }

        foreach ($this->rows() as $row) {
            $data = $row->getRow();
            array_splice($data, $index, 1);
            $row->setRow($data);
        }
    }

    public function reorderColumns(array $headerOrder): void
    {
        $currentHeader = $this->getHeaderRow()->getRow();
        $indexes = [];

        foreach ($headerOrder as $name) {
            $idx = array_search($name, $currentHeader, true);
            if ($idx === false) {
                throw new InvalidArgumentException("Column {$name} not in header");
            }
            $indexes[] = $idx;
        }

        foreach ($this->rows() as $row) {
            $data = $row->getRow();
            $reordered = [];
            foreach ($indexes as $i) {
                $reordered[] = $data[$i];
            }
            $row->setRow($reordered);
        }
    }

    public function formatDateTime(callable $column, string $format): void
    {
        $colIndex = $this->columnIndex($column);

        foreach ($this->getDataRows() as $row) {
            $data = $row->getRow();
            try {
                $data[$colIndex] = Carbon::parse((string) $data[$colIndex])->format($format);
            } catch (\Throwable) {
                $data[$colIndex] = '';
            }
            $row->setRow($data);
        }
    }

    public function truncateColumn(string $column, int $maxLength): void
    {
        $colIndex = $this->columnIndex($column);
        foreach ($this->getDataRows() as $row) {
            $this->truncateString($row, $colIndex, $maxLength);
        }
    }

    public function mergeFromTables(array $tables): void
    {
        if (empty($tables)) {
            return;
        }

        $merged = [];
        $expectedHeader = null;
        $expectedCount = null;

        foreach ($tables as $table) {
            if (!($table instanceof DataTable)) {
                throw new InvalidArgumentException('All items must be DataTable instances');
            }

            $rows = $table->rows();
            if (empty($rows)) {
                continue;
            }

            $first = $rows[0];
            $columnCount = count($first->getRow());

            if ($expectedHeader === null) {
                $expectedHeader = $first->getRow();
                $expectedCount = $columnCount;
                $merged = $rows;
                continue;
            }

            $rowsToAdd = $first->getRow() === $expectedHeader ? array_slice($rows, 1) : $rows;

            foreach ($rowsToAdd as $row) {
                if (count($row->getRow()) !== $expectedCount) {
                    throw new RuntimeException('Column count mismatch while merging tables');
                }
            }

            $merged = array_merge($merged, $rowsToAdd);
        }

        $this->rows = $merged;
    }

    public function innerJoin(DataTable $other, string $thisColumn, string $otherColumn, string $prefix = "_2"): void
    {
        $leftIdx  = $this->columnIndex($thisColumn);
        $rightIdx = $other->columnIndex($otherColumn);

        $rightLookup = [];
        foreach ($other->getDataRows() as $row) {
            $key = (string) $row->getRow()[$rightIdx];
            $rightLookup[$key][] = $row->getRow();
        }

        $leftHeader  = $this->getHeaderRow()->getRow();
        $rightHeader = $other->getHeaderRow()->getRow();

        $joinedHeader = $leftHeader;
        foreach ($rightHeader as $idx => $name) {
            if ($idx === $rightIdx) {
                continue;
            }
            $joinedHeader[] = in_array($name, $joinedHeader, true) ? $name . $prefix : $name;
        }

        $joinedRows = [$joinedHeader];

        foreach ($this->getDataRows() as $leftRow) {
            $leftData = $leftRow->getRow();
            $key = (string) $leftData[$leftIdx];

            if (!isset($rightLookup[$key])) {
                continue;
            }

            foreach ($rightLookup[$key] as $rightData) {
                $combined = $leftData;
                foreach ($rightData as $idx => $value) {
                    if ($idx === $rightIdx) {
                        continue;
                    }
                    $combined[] = $value;
                }
                $joinedRows[] = new Row($combined);
            }
        }
        $newRows = [];

        foreach ($joinedRows as $r) {
            if ($r instanceof Row) {
                $newRows[] = $r;
            } else {
                $newRows[] = new Row($r);
            }
        }

        $this->rows = $newRows;
    }

    public function encryptColumn(string $column, string $publicKey): void
    {
        $this->securityManager->setPublicKey($publicKey);
        $idx = $this->columnIndex($column);

        foreach ($this->getDataRows() as $row) {
            $d = $row->getRow();
            $d[$idx] = $this->securityManager->encryptValue($d[$idx]);
            $row->setRow($d);
        }
    }

    public function decryptColumn(string $column, string $privateKey): void
    {
        $this->securityManager->setPrivateKey($privateKey);
        $idx = $this->columnIndex($column);

        foreach ($this->getDataRows() as $row) {
            $d = $row->getRow();
            $d[$idx] = $this->securityManager->decryptValue($d[$idx]);
            $row->setRow($d);
        }
    }

    public function signColumn(string $column, string $privateKey, string $signatureColumn = "signature"): void
    {
        $colIdx = $this->columnIndex($column);

        $header = $this->getHeaderRow()->getRow();
        if (!in_array($signatureColumn, $header, true)) {
            $header[] = $signatureColumn;
            $this->getHeaderRow()->setRow($header);
        }

        $this->securityManager->setPrivateKey($privateKey);

        foreach ($this->getDataRows() as $row) {
            $data = $row->getRow();
            $data[] = $this->securityManager->singValue($data[$colIdx], $privateKey);
            $row->setRow($data);
        }
    }

    public function verifySignature(string $column, string $publicKey, string $signatureColumn = "signature"): void
    {
        $colIdx = $this->columnIndex($column);
        $sigIdx = $this->columnIndex($signatureColumn);

        $this->securityManager->setPublicKey($publicKey);

        foreach ($this->getDataRows() as $row) {
            $d = $row->getRow();
            if (!$this->securityManager->verify($d[$colIdx], $d[$sigIdx])) {
                throw new RuntimeException('Signature invalid');
            }
        }
    }

    private function normalizeRow(array $data, mixed $fill = ''): array
    {
        $expected = count($this->getHeaderRow()->getRow() ?? []);
        return $data ?: array_fill(0, $expected, $fill);
    }

    private function columnIndex(string $column): int
    {
        $idx = array_search($column, $this->getHeaderRow()->getRow(), true);
        if ($idx === false) {
            throw new InvalidArgumentException("Column {$column} missing");
        }
        return $idx;
    }

    private function getDataRows($skip = 1): array
    {
        return array_slice($this->rows, $skip);
    }

    private function getHeaderRow(): Row
    {
        return $this->rows[0];
    }

    public function rows(): array
    {
        return $this->rows;
    }
}
