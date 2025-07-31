<?php

namespace Irina\PhpDevStack\Traits;

use Irina\PhpDevStack\Classes\Row;

trait TruncateStringTrait
{
    public function truncateString(Row $currentRow, int $columnIndex, int $length): string
    {
        $rowData = $currentRow->getValues();
      //  print_r($rowData);
        if (isset($rowData[$columnIndex]) && is_string($rowData[$columnIndex])) {
            $rowData[$columnIndex] = substr($rowData[$columnIndex], 0, $length);
            $newValues[] = $rowData[$columnIndex];  
        }
        return (string)$rowData[$columnIndex];
    }
}
