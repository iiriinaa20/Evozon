<?php

namespace Irina\PhpDevStack\Classes;

use Irina\PhpDevStack\Contracts\FileReaderInterface;
use InvalidArgumentException;

class CsvReader implements FileReaderInterface
{
    public function read(string $path): array
    {
        $rows = [];
        $handle = fopen($path, 'r');
        $expectedColumns = null;
        $lineNumber = 0;

        while (($data = fgetcsv($handle)) !== false) {
            $lineNumber++;
            if ($expectedColumns === null) {
                $expectedColumns = count($data);
            } elseif (count($data) !== $expectedColumns) {
                throw new InvalidArgumentException("Row length isssue. Expected $expectedColumns, found " . count($data));
            }

            $rows[] = $data;
        }
        fclose($handle);
        return $rows;
    }
}
