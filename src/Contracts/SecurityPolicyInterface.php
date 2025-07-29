<?php

namespace Irina\PhpDevStack\Contracts;

interface SecurityPolicyInterface
{
    public function encryptValue(string $value);
    public function decryptValue(string $value);
    public function singValue(string $value);
    public function verify(string $value, string $signature);
}
