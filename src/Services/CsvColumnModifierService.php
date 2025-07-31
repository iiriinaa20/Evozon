<?php

namespace Irina\PhpDevStack\Services;

use Irina\PhpDevStack\Classes\CsvDataTable;
use Irina\PhpDevStack\Traits\FormatDateTrait;
use Irina\PhpDevStack\Traits\TruncateStringTrait;

class CsvColumnModifierService
{
    use TruncateStringTrait;
    use FormatDateTrait;

    public function __construct(private CsvDataTable $table) {}


    public function addIndexedColumn(): void
    {
        $rowCount = $this->table->getRowCount();
        $this->table->addColumn('index', 0, range(1, $rowCount));
    }

   public function truncateStringFromColumn(string $column,int $length,array $positions=[]): void
    {
        $rowCount = $this->table->getRowCount();
        $columnIndex =  $this->table->getColumnIndex($column);
        $positions = empty($positions) ? range(0,$rowCount-1) : $positions ;
        $formattedValues =[];
        foreach($this->table->getIterator() as $row)
        {
            $formattedValues[] = $this->truncateString($row, $columnIndex, $length);
        }
        print_r($formattedValues);
        $this->table->updateTableColumn($column,$formattedValues,$positions);
    }

     public function formatDateFromColumn(string $column,string $format,array $positions=[]): void
    {
        $rowCount = $this->table->getRowCount();
        $columnIndex =  $this->table->getColumnIndex($column);
        $positions = empty($positions) ? range(0,$rowCount-1) : $positions ;
        $formattedValues =[];
        foreach($this->table->getIterator() as $row)
        {
            $formattedValues[] = $this->formatDate($row,$columnIndex, $format);
        }
        $this->table->updateTableColumn($column,$formattedValues,$positions);
    }
}
