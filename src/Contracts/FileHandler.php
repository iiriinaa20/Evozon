<?php

namespace Irina\PhpDevStack\Contracts;

interface FileHandler
{

    public function readFile();
    public function writeFile(string $path);
}
