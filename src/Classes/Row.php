<?php

namespace Irina\PhpDevStack\Classes;

class Row
{

    public function __construct(private array $row)
    {
        
    }

    public function getRow(): array
    {
        return $this->row;
    }

    public function setRow(array $row): void
    {
        $this->row = $row;
    }
}