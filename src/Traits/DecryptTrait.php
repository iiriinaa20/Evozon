<?php

namespace Irina\PhpDevStack\Traits;

trait DecryptTrait
{
    private function decrypt(
        string $encryptedValue,
        string $privateKey
    ): string {
        if (!openssl_private_decrypt(base64_decode($encryptedValue), $decrypted, $privateKey)) {
            throw new \Exception("Failed to decrypt value.");
        }

        return $decrypted;
    }
}
