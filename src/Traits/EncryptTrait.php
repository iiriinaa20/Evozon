<?php

namespace Irina\PhpDevStack\Traits;

trait EncryptTrait
{
    public function encrypt(
        string $value,
        string $publicKey
    ): string {
        $pem = file_get_contents($publicKey);
        $key = openssl_pkey_get_public($pem);
        if (!openssl_public_encrypt($value, $encrypted, $key)) {
            throw new \Exception("Failed to encrypt value: $value");
        }

        return base64_encode($encrypted);
    }
}
