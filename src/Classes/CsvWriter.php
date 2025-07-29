<?php

namespace Irina\PhpDevStack\Classes;

use Irina\PhpDevStack\Contracts\FileWriterInterface;
use Irina\PhpDevStack\Classes\Row;
use Exception;

class CsvWriter implements FileWriterInterface
{
    private array $rows = [];

    public function __construct(private string $outPath) {}

    public function setRows(array $rows): void
    {
        if (empty($rows)) {
            throw new Exception("Cannot write empty row");
        }

        foreach ($rows as $row) {
            if (!$row instanceof Row) {
                throw new Exception("Must be instance of Row.");
            }
        }

        $this->rows = $rows;
    }

    public function write(): void
    {
        if (empty($this->rows)) {
            throw new Exception("setRows() before write()!");
        }

        $handle = fopen($this->outPath, 'w');

        if ($handle === false) {
            throw new Exception("Failed to open file for writing: {$this->outPath}");
        }

        $expectedColumns = count($this->rows[0]->getRow());

        foreach ($this->rows as $row) {
            $current = $row->getRow();
            if (count($current) !== $expectedColumns) {
                throw new Exception("Row length issues. Expected $expectedColumns, got " . count($current));
            }

            fputcsv($handle, $current);
        }

        fclose($handle);
    }
}
