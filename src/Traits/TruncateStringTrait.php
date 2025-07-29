<?php

namespace Irina\PhpDevStack\Traits;

use Irina\PhpDevStack\Classes\Row;

trait TruncateStringTrait
{
    public function truncateString(Row $currentRow, int $columnIndex, int $length): void
    {
        $rowData = $currentRow->getRow();
        if (isset($rowData[$columnIndex]) && is_string($rowData[$columnIndex])) {
            $rowData[$columnIndex] = substr($rowData[$columnIndex], 0, $length);
            $currentRow->setRow($rowData);
        }
    }
}
