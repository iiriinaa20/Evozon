<?php

namespace Irina\PhpDevStack\Services;

use DateTime;
use Irina\PhpDevStack\Classes\CsvDataTable;
use Irina\PhpDevStack\Classes\Row;
use Irina\PhpDevStack\Traits\FilterableTrait;

class CsvManipulateTablesService
{
    use FilterableTrait;
    public function __construct(private CsvDataTable $tableP) {}


    public function mergeFromTables(array $tables): void
    {

        $tables_headers = [];
        $tables_row_size = [];

        foreach ($tables as $key => $table) {
            if (!($table instanceof CsvDataTable)) {
                continue;
            }

            if ($table->getHasHeaders()) {
                $tables_headers[] = $table->getHeader();
            }

            $tables_row_size[] = $table->getIterator()[0]->getValues();
            // $table->getIterator()->rewind();
        }

        if ((count($tables_headers) > 0) && (count($tables) === count($tables_headers))) {
            // f1
            $normalize = function ($arr) {
                if (!is_array($arr)) return null;
                $tmp = array_values($arr);
                $tmp = array_map(fn($v) => is_string($v) ? mb_strtolower($v, 'UTF-8') : $v, $tmp);
                sort($tmp, SORT_REGULAR);
                return $tmp;
            };

            if (!$this->areHeadersEqual($tables_headers, $normalize)) {
                throw new \RuntimeException("Headers missmatch");
            }
            $baselineHeader = $tables_headers[0];
            $this->tableP->addHeader($baselineHeader);
            foreach ($tables as $key => $table) {
                if (!($table instanceof CsvDataTable)) {
                    continue;
                }
                $table->reorderColumns($baselineHeader);
                foreach ($table->getIterator() as $line) {
                    if (!($line instanceof Row)) {
                        continue;
                    }
                    $this->tableP->addRow($line->getValues());
                }
            }
        } else {
            // f2
            $normalize = function ($arr) {
                if (!is_array($arr)) return null;
                return count($arr);
            };
            // print_r($this->areHeadersEqual($tables_row_size, $normalize));
            // print_r($tables_row_size);
            if (!$this->areHeadersEqual($tables_row_size, $normalize)) {
                throw new \RuntimeException("Lines length missmatch");
            }
            foreach ($tables as $key => $table) {
                if (!($table instanceof CsvDataTable)) {
                    continue;
                }
                foreach ($table->getIterator() as $line) {
                    if (!($line instanceof Row)) {
                        continue;
                    }
                    $this->tableP->addRow($line->getValues());
                }
            }
        }
    }

    private function areHeadersEqual(array $headers, callable $normalize): bool
    {
        if (count($headers) <= 1) {
            return true;
        }

        $baseline = $normalize($headers[0]);
        if ($baseline === null) {
            return false;
        }

        foreach ($headers as $header) {
            $cur = $normalize($header);
            if ($cur === null || $cur !== $baseline) {
                return false;
            }
        }

        return true;
    }

    public function innerJoinTables(CsvDataTable $secondTable, string $firstKey, string $secondKey): void
    {
        $firstKeyValues = [];
        //  var_dump($this->tableP);

        //  var_dump($secondTable);
        $firstKeyIndex = $this->tableP->getColumnIndex($firstKey);
        $secondKeyIndex = $secondTable->getColumnIndex($secondKey);
        $isHeaderUpdated = false;
        // print_r($firstKeyIndex);
        // print_r($secondKeyIndex);
        foreach ($this->tableP->getIterator() as $line) {
            $firstKeyValues[] = $line->getValues()[$firstKeyIndex];
        }
        $firstKeyValues = array_unique($firstKeyValues);
        // var_dump($firstKeyValues);

        $firstHeaderCount = count($this->tableP->getHeader());
        $firstTableRows = iterator_to_array($this->tableP->getIterator(), true);
        foreach ($secondTable->getIterator() as $line) {
            if (!($line instanceof Row)) {
                continue;
            }

            $secondLineValues = $line->getValues();
            if (in_array($secondLineValues[$secondKeyIndex], $firstKeyValues)) {
                if (!$isHeaderUpdated) {
                    $newHeader = array_merge($this->tableP->getHeader(), $secondTable->getHeader());
                    $this->tableP->addHeader($newHeader);
                    $isHeaderUpdated = true;
                }
                $L1 = array_search($line->getValues()[$secondKeyIndex], iterator_to_array($this->tableP->getIterator(), true));
                //indecsiii la liniile ce bat
                $keys = array_keys(array_filter($firstTableRows, fn($r) => $r->getValues()[$firstKeyIndex] === $secondLineValues[$secondKeyIndex]));
                foreach ($keys as $k) {
                    // var_dump($firstTableRows[$k]->getValues(), $k);
                    $newLine = array_merge($firstTableRows[$k]->getValues(), $secondLineValues);
                    $this->tableP->updateRow($newLine, $k);
                }
            }
        }


        // normalizam linile ca sa fill la ce e gol
        foreach ($firstTableRows as $k => $row) {
            $this->tableP->updateRow(array_merge($row->getValues(), []), $k);
        }
        $this->tableP->removeColumnByPosition($firstHeaderCount + $secondKeyIndex);
    }

    public function handleQuery(array $params, array $headers, array $rows): void
    {
        $column = $params['column'] ?? null;
        $op     = $params['op'] ?? '=';
        $value  = $params['value'] ?? null;
        $select = $params['get'] ?? null;

        if (!$column || !$value || !$select) {
            throw new \InvalidArgumentException("Missing one of the required parameters: column, value, get");
        }

        $columnIndex = array_search($column, $headers);
        if ($columnIndex === false) {
            throw new \InvalidArgumentException("Column '$column' not found in headers");
        }

        $columnValues = array_column($rows, $columnIndex);
        $type = $this->detectType($value);

        // var_dump($rows);
        // var_dump($columnIndex);
        // var_dump($type);
        // var_dump($columnValues);    
        $data = $this
            ->applyOperator($op, $rows, $value, $type, $columnIndex)
            ->select($headers, ...array_map('trim', explode(',', $select)));
        // var_dump($this->tableP);
        foreach ($this->tableP->getIterator() as $key => $value) {
            $this->tableP->removeRow(0);
        }
        $this->tableP->addHeader(array_keys($data[0] ?? []));
        // var_dump($this->tableP);
        // var_dump($data);
        foreach ($data as $key => $value) {
            $this->tableP->addRow(array_values($value));
        }
    }

    private function detectType(string $value): string
    {
        if (is_numeric($value)) return 'number';

        try {
            new DateTime($value);
            return 'object';
        } catch (\Exception) {
            // nu e data
        }

        return 'string';
    }
}


////////////<,>,=,<=,>=
/////////// int uri ca intr uri normale
////////// stringuri cu length
////////// stringuri cu length