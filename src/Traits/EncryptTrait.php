<?php

namespace Irina\PhpDevStack\Traits;

trait EncryptTrait
{
    public function encrypt(
        string $value,
        string $publicKey
    ): string {
        if (!openssl_public_encrypt($value, $encrypted, $publicKey)) {
            throw new \Exception("Failed to encrypt value: $value");
        }

        return base64_encode($encrypted);
    }
}
