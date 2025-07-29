<?php

namespace Irina\PhpDevStack\Classes;

use Irina\PhpDevStack\Contracts\SecurityPolicyInterface;
use Irina\PhpDevStack\Traits\DecryptTrait;
use Irina\PhpDevStack\Traits\EncryptTrait;
use Irina\PhpDevStack\Traits\SignVerifyTrait;

class SecurityManager implements SecurityPolicyInterface
{
    use EncryptTrait, DecryptTrait, SignVerifyTrait;

    private string $privateKeyPem;
    private string $publicKeyPem;

    public function setPrivateKey(string $privateKeyPath)
    {
        $this->privateKeyPem = $this->loadKey($privateKeyPath);
    }
    public function setPublicKey(string $publicKeyPath)
    {
        $this->publicKeyPem = $this->loadKey($publicKeyPath);
    }

    private function loadKey(string $keyPath): string
    {
        if (!is_file($keyPath)) {
            throw new \Exception("Key file not found: {$keyPath}");
        }

        $key = file_get_contents($keyPath);
        if ($key === false || $key === '') {
            throw new \Exception("Could not load key from: {$keyPath}");
        }

        return $key;
    }


    public function encryptValue(string $value)
    {
        return $this->encrypt($value, $this->publicKeyPem);
    }

    public function decryptValue(string $value): string
    {
        return $this->decrypt($value, $this->privateKeyPem);
    }

    public function singValue(string $value)
    {
        return $this->sign($value, $this->privateKeyPem);
    }

    public function verify(string $value, string $signature)
    {
        return $this->verifySignature($value, $signature, $this->publicKeyPem);
    }
}
