<?php

namespace Irina\PhpDevStack\Classes;

use Irina\PhpDevStack\Contracts\FileWriterInterface;
use Irina\PhpDevStack\Classes\Row;
use Exception;

class CsvWriter implements FileWriterInterface
{
    private array $rows = [];

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

    public function write(string $outPath): void
    {
        if (empty($this->rows)) {
            throw new Exception("setRows() before write()!");
        }

        $handle = fopen($outPath, 'w');

        if ($handle === false) {
            throw new Exception("Failed to open file for writing: {$outPath}");
        }

        $expectedColumns = count($this->rows[0]->getHeader());
        if ($expectedColumns !== 0) {
            fputcsv($handle, $this->rows[0]->getHeader());
        }

        foreach ($this->rows as $row) {
            $current = $row->getValues();
            if ((count($current) !== $expectedColumns) && ($expectedColumns !== 0)) {
                throw new Exception("Row length issues. Expected $expectedColumns, got " . count($current));
            }

            fputcsv($handle, $current);
        }

        fclose($handle);
    }
}
