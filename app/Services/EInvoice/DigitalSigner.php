<?php

namespace App\Services\EInvoice;

use Illuminate\Support\Facades\Storage;

class DigitalSigner
{
    /**
     * Create a new service instance.
     *
     * @return void
     */
    protected string $certificate;

    protected string $privateKey;

    protected string $privateKeyPassphrase;

    public function __construct()
    {
        $this->certificate = Storage::disk('digital_certificates_disk')->get('ncc_solutions_sdn_bhd.cer');
        $this->privateKey = Storage::disk('digital_certificates_disk')->get('ncc_solutions_sdn_bhd.privkey.pem');
        $this->privateKeyPassphrase = config('services.einvoice.private_key_passphrase');
    }

    /**
     * Example method for the service.
     */
    public function signDocument($document): string
    {
        $privateKey = openssl_pkey_get_private($this->privateKey, $this->privateKeyPassphrase);
        $documentTransformer = new DocumentTransformer;
        $jsonDocument = $documentTransformer->json_encode($document);
        $signature = '';
        openssl_sign($jsonDocument, $signature, $privateKey, OPENSSL_ALGO_SHA256);

        return $documentTransformer->base64_encode($signature);
    }

    public function signCertificate()
    {
        $certHash = openssl_x509_fingerprint($this->certificate, 'sha256');
        $documentTransformer = new DocumentTransformer;

        return $documentTransformer->base64_encode($documentTransformer->hex2Bin($certHash));
    }

    public function getCertificateDetails()
    {
        return openssl_x509_parse(openssl_x509_read($this->certificate));
    }

    public function getCertificateRawData()
    {
        $rawCertData = $this->certificate;
        $documentTransformer = new DocumentTransformer;
        if (str_starts_with($rawCertData, '-----BEGIN CERTIFICATE-----')) {
            $rawCertData = preg_replace('/-----.*-----/', '', $rawCertData);
            $rawCertData = $documentTransformer->base64_decode($rawCertData);
        }

        return $documentTransformer->base64_encode($rawCertData);
    }
}
