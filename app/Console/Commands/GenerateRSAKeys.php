<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateRSAKeys extends Command
{
    protected $signature = 'rsa:generate';
    protected $description = 'Generate RSA key pair';

    public function handle()
    {
        $keyPair = openssl_pkey_new([
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);

        openssl_pkey_export($keyPair, $privateKey);
        $publicKeyDetails = openssl_pkey_get_details($keyPair);
        $publicKey = $publicKeyDetails['key'];

        Storage::disk('local')->put('rsa_private.pem', $privateKey);
        Storage::disk('local')->put('rsa_public.pem', $publicKey);

        $this->info('RSA key pair generated in storage/app/');
    }
}
