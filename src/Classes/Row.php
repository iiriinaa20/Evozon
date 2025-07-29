<?php

namespace Irina\PhpDevStack\Classes;

class Row
{
    public function __construct(
        private array $values = [],
        private array &$header = [],
    ) {}

    public function getHeader(): array
    {
        return $this->header;
    }

    public function setHeader(array $header): void
    {
        $this->header = $header;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function setValues(array $values): void
    {
        $this->values = $values;
    }

    public function getRowWithHeaders(): array
    {
        return array_combine($this->header, $this->values);
    }

    public function setRowWithHeaders(array $data): void
    {
        static::setHeader(array_keys($data));
        static::setValues(array_values($data));
    }
}
