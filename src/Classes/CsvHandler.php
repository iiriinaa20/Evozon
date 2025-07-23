<?php


namespace Irina\PhpDevStack\Classes;

use Irina\PhpDevStack\Contracts\FileHandler;

class CsvHandler implements FileHandler
{

    public static function fromFile(string $path): CsvHandler
    {
        return new CsvHandler($path);
    }

    public function __construct(
        private string $path,
        private array $rows = [],

    ) {}

    public function readFile()
    {
        // TBD --- VALIDARI + ERRORS HANDLING
        $handler = fopen($this->path, 'r');
        $this->rows = [];

        while (($row = fgetcsv($handler)) !== false) {
            $this->rows[] = $row;
        }

        fclose($handler);
    }

    public function writeFile(string $outPath)
    {

        // TBD --- VALIDARI ERORI
        $handle = fopen($outPath, 'w');
        foreach ($this->rows as $row)
            fputcsv($handle, $row);
        fclose($handle);
    }


    // citire prince referinta, mai rapid si eficient :|
    public function &getRows(): array
    {
        return $this->rows;
    }
}
