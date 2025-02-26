<?php

namespace App\Service;

use Random\RandomException;

class EncryptionService
{
    private string $secretKey;
    private string $cipher = 'AES-256-CBC';

    public function __construct(string $secretKey)
    {
        if (empty($secretKey) || strlen($secretKey) < 32) {
            throw new \RuntimeException('La clé de chiffrement doit faire 32 caractères.');
        }

        $this->secretKey = hash('sha256', $secretKey); // On s'assure d'une clé AES-256 correcte
    }

    public function encrypt(string $data): string
    {
        try {
            $iv = random_bytes(openssl_cipher_iv_length($this->cipher));
        } catch (RandomException $e) {
            throw new \RuntimeException('Impossible de générer un vecteur d\'initialisation.');
        }
        $encrypted = openssl_encrypt($data, $this->cipher, $this->secretKey, 0, $iv);

        if ($encrypted === false) {
            throw new \RuntimeException('Échec du chiffrement des données.');
        }

        return base64_encode($iv . $encrypted);
    }

    public function decrypt(string $encryptedData): string
    {
        $decoded = base64_decode($encryptedData);
        $ivLength = openssl_cipher_iv_length($this->cipher);
        $iv = substr($decoded, 0, $ivLength);
        $cipherText = substr($decoded, $ivLength);

        $decrypted = openssl_decrypt($cipherText, $this->cipher, $this->secretKey, 0, $iv);

        if ($decrypted === false) {
            throw new \RuntimeException('Échec du déchiffrement des données.');
        }

        return $decrypted;
    }
}
