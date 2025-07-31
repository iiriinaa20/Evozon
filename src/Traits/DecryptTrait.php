<?php

namespace Irina\PhpDevStack\Traits;

trait DecryptTrait
{
    private function decrypt(
        string $encryptedValue,
        string $privateKey
    ): string {
        $pem = file_get_contents($privateKey);
        $key = openssl_pkey_get_private($pem, $passphrase ?? '');
        if (!openssl_private_decrypt(base64_decode($encryptedValue), $decrypted, $key)) {
            throw new \Exception("Failed to decrypt value.");
        }

        return $decrypted;
    }
}
