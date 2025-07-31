<?php

namespace Irina\PhpDevStack\Services;

use Irina\PhpDevStack\Classes\CsvDataTable;
use Irina\PhpDevStack\Classes\CsvReader;
use Irina\PhpDevStack\Traits\DecryptTrait;
use Irina\PhpDevStack\Traits\EncryptTrait;
use Irina\PhpDevStack\Traits\FormatDateTrait;
use Irina\PhpDevStack\Traits\SignVerifyTrait;
use Irina\PhpDevStack\Traits\TruncateStringTrait;

class CsvEncryptDecryptService
{

    use EncryptTrait;
    use DecryptTrait;
    use SignVerifyTrait;
    public function __construct(private CsvDataTable $table) {}

    public function encryptColumnData(string $column, string $pubkey, array $positions = []): void
    {
        $rowCount = $this->table->getRowCount();
        $columnIndex =  $this->table->getColumnIndex($column);
        $positions = empty($positions) ? range(0, $rowCount - 1) : $positions;
        $formattedValues = [];
        foreach ($this->table->getIterator() as $row) {
            $rowArr = $row->getValues();
            $value = $rowArr[$columnIndex];
            $encryptedValues[] = $this->encrypt($value, $pubkey);
        }
        $this->table->updateTableColumn($column, $encryptedValues, $positions);
    }

    public function decryptColumnData(string $column, string $privkey, array $positions = []): void
    {
        $rowCount = $this->table->getRowCount();
        $columnIndex =  $this->table->getColumnIndex($column);
        $positions = empty($positions) ? range(0, $rowCount - 1) : $positions;
        $formattedValues = [];
        foreach ($this->table->getIterator() as $row) {
            $rowArr = $row->getValues();
            $value = $rowArr[$columnIndex];
            $decryptedValues[] = $this->decrypt($value, $privkey);
        }
        $this->table->updateTableColumn($column, $decryptedValues, $positions);
    }

    public function addSignatureToColumn(string $column, string $privkey, array $positions = []): void
    {
        $rowCount = $this->table->getRowCount();
        $columnIndex =  $this->table->getColumnIndex($column);
        $positions = empty($positions) ? range(0, $rowCount - 1) : $positions;
        $formattedValues = [];
        foreach ($this->table->getIterator() as $row) {
            $rowArr = $row->getValues();
            $value = $rowArr[$columnIndex];
            $decryptedValues[] = $this->decrypt($value, $privkey);
        }
        $this->table->updateTableColumn($column, $decryptedValues, $positions);
    }

    public function verifySignedColumn(string $column, string $pubkey, array $positions = [],string $signature='signature'): void
    {
        $rowCount = $this->table->getRowCount();
        $columnIndex =  $this->table->getColumnIndex($column);
        $signatureIndex = $this->table->getColumnIndex($signature);

        $positions = empty($positions) ? range(0, $rowCount - 1) : $positions;
        $formattedValues = [];
        foreach ($this->table->getIterator() as $row) {
            $rowArr = $row->getValues();
            $value = $rowArr[$columnIndex];
            $valueSignature = $rowArr[$signatureIndex];
            $decryptedValues[] = $this->verifySignature($value,$valueSignature,$pubkey);
        }
        $this->table->updateTableColumn($column, $decryptedValues, $positions);
    }
}
