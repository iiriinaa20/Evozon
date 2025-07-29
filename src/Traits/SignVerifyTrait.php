<?php

namespace Irina\PhpDevStack\Traits;

trait SignVerifyTrait
{
    public function sign(string $value, string $privateKey): string
    {
        $ok = openssl_sign($value, $signature, $privateKey, OPENSSL_ALGO_SHA256);
        if (!$ok) {
            throw new \Exception("Signing failed.");
        }
        return base64_encode($signature);
    }

    public function verifySignature(string $value, string $signature, string $publicKey): bool
    {
        $result = openssl_verify($value, base64_decode($signature), $publicKey, OPENSSL_ALGO_SHA256);

        if ($result === 1) {
            return true; 
        } elseif ($result === 0) {
            return false; 
        } else {
            throw new \Exception("OpenSSL verification error.");
        }
    }
}
