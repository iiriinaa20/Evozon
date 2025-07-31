<?php

namespace Irina\PhpDevStack\Traits;

use Irina\PhpDevStack\Classes\Row;
use Carbon\Carbon;

trait FormatDateTrait
{
    public function formatDate(Row $currentRow, int $columnIndex, string $format)
    {
        $rowData = $currentRow->getValues();
        if (isset($rowData[$columnIndex])) {
            $rowData[$columnIndex] = Carbon::parse($rowData[$columnIndex])->format($format);
            return $rowData[$columnIndex];
        }
       // print_r($newValues);
    }
}
