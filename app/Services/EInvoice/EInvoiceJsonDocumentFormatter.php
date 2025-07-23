<?php

namespace App\Services\EInvoice;

use App\Enums\DocumentType;
use App\Models\EinvoiceHeader;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class EInvoiceJsonDocumentFormatter
{
    /**
     * Create a new service instance.
     *
     * @return void
     */

    /**
     * Example method for the service.
     */
    protected float $totalExcTaxAmt;

    protected float $totalIncTaxAmt;

    protected float $totalPayableAmt;

    protected float $totalNetAmt;

    protected float $totalDiscAmt;

    protected float $totalChargeAmt;

    protected float $totalTaxableAmtPerTaxType;

    protected float $totalTaxAmtPerTaxType;

    public function __construct()
    {
        $this->totalExcTaxAmt = 0;
        $this->totalIncTaxAmt = 0;
        $this->totalPayableAmt = 0;
        $this->totalNetAmt = 0;
        $this->totalDiscAmt = 0;
        $this->totalChargeAmt = 0;
        $this->totalTaxableAmtPerTaxType = 0;
        $this->totalTaxAmtPerTaxType = 0;
    }

    public function generateSignedDocument($signedDocument, $signedDocumentDigest, $certificateRawData, $hashedSignedProperties, $documentDigest, $certificateDetails)
    {
        $signedDocument['Invoice'][0]['UBLExtensions'][0]['UBLExtension'][0]['ExtensionContent'][0]['UBLDocumentSignatures'][0]['SignatureInformation'][0]['Signature'][0]['SignatureValue'][0]['_'] = $signedDocumentDigest;
        $signedDocument['Invoice'][0]['UBLExtensions'][0]['UBLExtension'][0]['ExtensionContent'][0]['UBLDocumentSignatures'][0]['SignatureInformation'][0]['Signature'][0]['KeyInfo'][0]['X509Data'][0]['X509Certificate'][0]['_'] = $certificateRawData;
        $signedDocument['Invoice'][0]['UBLExtensions'][0]['UBLExtension'][0]['ExtensionContent'][0]['UBLDocumentSignatures'][0]['SignatureInformation'][0]['Signature'][0]['SignedInfo'][0]['Reference'][0]['DigestValue'][0]['_'] = $hashedSignedProperties;
        $signedDocument['Invoice'][0]['UBLExtensions'][0]['UBLExtension'][0]['ExtensionContent'][0]['UBLDocumentSignatures'][0]['SignatureInformation'][0]['Signature'][0]['SignedInfo'][0]['Reference'][1]['DigestValue'][0]['_'] = $documentDigest;
        if (in_array(request()->getHost(), ['localhost', '127.0.0.1', '192.168.122.72'])) {
            $organizationIdentifier = $certificateDetails['subject']['organizationIdentifier'];
        } else {
            $organizationIdentifier = $certificateDetails['subject']['UNDEF'];
        }
        $signedDocument['Invoice'][0]['UBLExtensions'][0]['UBLExtension'][0]['ExtensionContent'][0]['UBLDocumentSignatures'][0]['SignatureInformation'][0]['Signature'][0]['KeyInfo'][0]['X509Data'][0]['X509SubjectName'][0]['_'] = 'CN=' . $certificateDetails['subject']['CN'] . ', SERIALNUMBER=' . $certificateDetails['subject']['serialNumber'] . ', O=' . $certificateDetails['subject']['O'] . ', OID.2.5.4.97=' . $organizationIdentifier . ', C=' . $certificateDetails['subject']['C'];
        $signedDocument['Invoice'][0]['UBLExtensions'][0]['UBLExtension'][0]['ExtensionContent'][0]['UBLDocumentSignatures'][0]['SignatureInformation'][0]['Signature'][0]['KeyInfo'][0]['X509Data'][0]['X509IssuerSerial'][0]['X509IssuerName'][0]['_'] = 'CN=' . $certificateDetails['issuer']['CN'] . ', O=' . $certificateDetails['issuer']['O'] . ', C=' . $certificateDetails['issuer']['C'];
        $signedDocument['Invoice'][0]['UBLExtensions'][0]['UBLExtension'][0]['ExtensionContent'][0]['UBLDocumentSignatures'][0]['SignatureInformation'][0]['Signature'][0]['KeyInfo'][0]['X509Data'][0]['X509IssuerSerial'][0]['X509SerialNumber'][0]['_'] = $certificateDetails['serialNumber'];
        $signedDocument['Invoice'][0]['Signature'] = [
            [
                'ID' => [
                    ['_' => 'urn:oasis:names:specification:ubl:signature:Invoice'],
                ],
                'SignatureMethod' => [
                    ['_' => 'urn:oasis:names:specification:ubl:dsig:enveloped:xades'],
                ],
            ],
        ];

        return $signedDocument;
    }

    public function appendSignatureToJsonDocument($document, $hashCertificate, $certificateDetails)
    {
        $document['Invoice'][0]['UBLExtensions'][0] = [
            'UBLExtension' => [
                [
                    'ExtensionURI' => [
                        [
                            '_' => 'urn:oasis:names:specification:ubl:dsig:enveloped:xades',
                        ],
                    ],
                    'ExtensionContent' => [
                        [
                            'UBLDocumentSignatures' => [
                                [
                                    'SignatureInformation' => [
                                        [
                                            'ID' => [
                                                [
                                                    '_' => 'urn:oasis:names:specification:ubl:signature:1',
                                                ],
                                            ],
                                            'ReferencedSignatureID' => [
                                                [
                                                    '_' => 'urn:oasis:names:specification:ubl:signature:Invoice',
                                                ],
                                            ],
                                            'Signature' => [
                                                [
                                                    'Id' => 'signature',
                                                    'Object' => [
                                                        [
                                                            'QualifyingProperties' => [
                                                                [
                                                                    'Target' => 'signature',
                                                                    'SignedProperties' => [
                                                                        [
                                                                            'Id' => 'id-xades-signed-props',
                                                                            'SignedSignatureProperties' => [
                                                                                [
                                                                                    'SigningTime' => [
                                                                                        [
                                                                                            '_' => Carbon::now()->setTimezone(config('services.einvoice.timezone'))->subSeconds(1)->format('Y-m-d') . 'T' . Carbon::now()->setTimezone(config('services.einvoice.timezone'))->subSeconds(1)->format('H:i:s') . 'Z',
                                                                                        ],
                                                                                    ],
                                                                                    'SigningCertificate' => [
                                                                                        [
                                                                                            'Cert' => [
                                                                                                [
                                                                                                    'CertDigest' => [
                                                                                                        [
                                                                                                            'DigestMethod' => [
                                                                                                                [
                                                                                                                    '_' => '',
                                                                                                                    'Algorithm' => 'http://www.w3.org/2001/04/xmlenc#sha256',
                                                                                                                ],
                                                                                                            ],
                                                                                                            'DigestValue' => [
                                                                                                                [
                                                                                                                    '_' => $hashCertificate,
                                                                                                                ],
                                                                                                            ],
                                                                                                        ],
                                                                                                    ],
                                                                                                    'IssuerSerial' => [
                                                                                                        [
                                                                                                            'X509IssuerName' => [
                                                                                                                [
                                                                                                                    '_' => 'CN=' . $certificateDetails['issuer']['CN'] . ', O=' . $certificateDetails['issuer']['O'] . ', C=' . $certificateDetails['issuer']['C'],
                                                                                                                ],
                                                                                                            ],
                                                                                                            'X509SerialNumber' => [
                                                                                                                [
                                                                                                                    '_' => $certificateDetails['serialNumber'],
                                                                                                                ],
                                                                                                            ],
                                                                                                        ],
                                                                                                    ],
                                                                                                ],
                                                                                            ],
                                                                                        ],
                                                                                    ],
                                                                                ],
                                                                            ],
                                                                        ],
                                                                    ],
                                                                ],
                                                            ],
                                                        ],
                                                    ],
                                                    'KeyInfo' => [
                                                        [
                                                            'X509Data' => [
                                                                [
                                                                    'X509Certificate' => [
                                                                        [
                                                                            '_' => '',
                                                                        ],
                                                                    ],
                                                                    'X509SubjectName' => [
                                                                        [
                                                                            '_' => '',
                                                                        ],
                                                                    ],
                                                                    'X509IssuerSerial' => [
                                                                        [
                                                                            'X509IssuerName' => [
                                                                                [
                                                                                    '_' => '',
                                                                                ],
                                                                            ],
                                                                            'X509SerialNumber' => [
                                                                                [
                                                                                    '_' => '',
                                                                                ],
                                                                            ],
                                                                        ],
                                                                    ],
                                                                ],
                                                            ],
                                                        ],
                                                    ],
                                                    'SignatureValue' => [
                                                        [
                                                            '_' => 'WHSE7GWLP1aEhV4s5W3vB7x+9TDJmwtu0WkerL7IXYOhSWnuhJUNcRUwQpl5T/hkKXMvswqVHTQkQRXfhZFb+Wta0Z/9qFQQCUAOuxcP/at9YnRmxuAarCS3rIvegNhPvcXtx0sfpqmLQdEV7Wegb6QlUiI4HiFwcfSqIF4/L9odjqEgen28S8Flf28K+LsnpB8rRHk+0UKNfNALxZRhZ/cE123gpW/sxgz3LfZeSByNAjv4ARWxoG/fpVriib1PML609raqgYNcab0EhxUoh6W07pE+JsAhdk235kJmea0rvj5kaMG6petujddASJcurm6FhsphNWz533NLp3g3Zg==',
                                                        ],
                                                    ],
                                                    'SignedInfo' => [
                                                        [
                                                            'SignatureMethod' => [
                                                                [
                                                                    '_' => '',
                                                                    'Algorithm' => 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256',
                                                                ],
                                                            ],
                                                            'Reference' => [
                                                                [
                                                                    'Type' => 'http://uri.etsi.org/01903/v1.3.2#SignedProperties',
                                                                    'URI' => '#id-xades-signed-props',
                                                                    'DigestMethod' => [
                                                                        [
                                                                            '_' => '',
                                                                            'Algorithm' => 'http://www.w3.org/2001/04/xmlenc#sha256',
                                                                        ],
                                                                    ],
                                                                    'DigestValue' => [
                                                                        [
                                                                            '_' => 'V1N5uDG4HLu/Ffzr2YK6tF5MlKD2aD8dIfhtOhvbhBc=',
                                                                        ],
                                                                    ],
                                                                ],
                                                                [
                                                                    'Type' => '',
                                                                    'URI' => '',
                                                                    'DigestMethod' => [
                                                                        [
                                                                            '_' => '',
                                                                            'Algorithm' => 'http://www.w3.org/2001/04/xmlenc#sha256',
                                                                        ],
                                                                    ],
                                                                    'DigestValue' => [
                                                                        [
                                                                            '_' => 'eewsoocAY7O71SW5DEHxqmXGRA+StpGIMywbk3rUT+w=',
                                                                        ],
                                                                    ],
                                                                ],
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return $document;
    }

    public function generateJsonDocument(EinvoiceHeader $eInvoiceHeader, Collection $eInvoiceDetail, DocumentType $documentType): array
    {
        $invoiceLines = $this->populateInvoiceLines($eInvoiceDetail, $eInvoiceHeader);
        $billingReference = $this->populateBillingRefence($eInvoiceHeader, $documentType);
        $additionalDiscountAmount = $eInvoiceHeader->EINV_TOTAL_ADD_DISC_AMT ? floatval($eInvoiceHeader->EINV_TOTAL_ADD_DISC_AMT) : 0;
        $additionalFeeAmount = $eInvoiceHeader->EINV_TOTAL_ADD_FEE_AMT ? floatval($eInvoiceHeader->EINV_TOTAL_ADD_FEE_AMT) : 0;
        $currencyCode = $eInvoiceHeader->EINV_CURR;
        $data = [
            '_D' => 'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2',
            '_A' => 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            '_B' => 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            'Invoice' => [
                [
                    'ID' => [
                        [
                            '_' => $eInvoiceHeader->EINV_ID,
                        ],
                    ],
                    'IssueDate' => [
                        [
                            '_' => Carbon::now()->setTimezone(config('services.einvoice.timezone'))->subSeconds(1)->format('Y-m-d'),
                        ],
                    ],
                    'IssueTime' => [
                        [
                            '_' => Carbon::now()->setTimezone(config('services.einvoice.timezone'))->subSeconds(1)->format('H:i:s') . 'Z',
                        ],
                    ],
                    'InvoiceTypeCode' => [
                        [
                            '_' => $eInvoiceHeader->EINV_TYPE,
                            'listVersionID' => $eInvoiceHeader->EINV_VERSION,
                        ],
                    ],
                    'DocumentCurrencyCode' => [
                        [
                            '_' => $currencyCode,
                        ],
                    ],
                    'TaxCurrencyCode' => [
                        [
                            '_' => $currencyCode,
                        ],
                    ],
                    'InvoicePeriod' => [
                        [
                            'StartDate' => [
                                [
                                    '_' => '',
                                ],
                            ],
                            'EndDate' => [
                                [
                                    '_' => '',
                                ],
                            ],
                            'Description' => [
                                [
                                    '_' => $eInvoiceHeader->EINV_FREQ ?? '',
                                ],
                            ],
                        ],
                    ],
                    'BillingReference' => $billingReference,
                    'AdditionalDocumentReference' => [
                        [
                            'ID' => [
                                [
                                    '_' => $eInvoiceHeader->EINV_REF_CUSTOM ?? '',
                                ],
                            ],
                            'DocumentType' => [
                                [
                                    '_' => 'CustomsImportForm',
                                ],
                            ],
                        ],
                        [
                            'ID' => [
                                [
                                    '_' => $eInvoiceHeader->EINV_FTA ? 'FTA' : '',
                                ],
                            ],
                            'DocumentType' => [
                                [
                                    '_' => 'FreeTradeAgreement',
                                ],
                            ],
                            'DocumentDescription' => [
                                [
                                    '_' => $eInvoiceHeader->EINV_FTA ?? '',
                                ],
                            ],
                        ],
                        [
                            'ID' => [
                                [
                                    '_' => $eInvoiceHeader->EINV_REF_CUSTOM_2 ?? '',
                                ],
                            ],
                            'DocumentType' => [
                                [
                                    '_' => 'K2',
                                ],
                            ],
                        ],
                        [
                            'ID' => [
                                [
                                    '_' => $eInvoiceHeader->EINV_INCOTERMS ?? '',
                                ],
                            ],
                        ],
                    ],
                    'AccountingSupplierParty' => [
                        [
                            'AdditionalAccountID' => [
                                [
                                    '_' => $eInvoiceHeader->EINV_AUTH_CERT ?? '',
                                    'schemeAgencyName' => 'CertEX',
                                ],
                            ],
                            'Party' => [
                                [
                                    'IndustryClassificationCode' => [
                                        [
                                            '_' => $eInvoiceHeader->EINV_SUP_MSIC,
                                            'name' => $eInvoiceHeader->EINV_SUP_BUS_ACT_DESC,
                                        ],
                                    ],
                                    'PartyIdentification' => [
                                        [
                                            'ID' => [
                                                [
                                                    '_' => preg_replace('/\s+/', '', $eInvoiceHeader->EINV_SUP_TIN),
                                                    'schemeID' => 'TIN',
                                                ],
                                            ],
                                        ],
                                        [
                                            'ID' => [
                                                [
                                                    '_' => $eInvoiceHeader->EINV_SUP_ROC,
                                                    'schemeID' => $eInvoiceHeader->EINV_SUP_REG_TYPE,
                                                ],
                                            ],
                                        ],
                                        [
                                            'ID' => [
                                                [
                                                    '_' => $eInvoiceHeader->EINV_SUP_SST,
                                                    'schemeID' => 'SST',
                                                ],
                                            ],
                                        ],
                                        [
                                            'ID' => [
                                                [
                                                    '_' => $eInvoiceHeader->EINV_SUP_TTX,
                                                    'schemeID' => 'TTX',
                                                ],
                                            ],
                                        ],
                                    ],
                                    'PostalAddress' => [
                                        [
                                            'CityName' => [
                                                [
                                                    '_' => $eInvoiceHeader->EINV_SUP_CITY ?? '',
                                                ],
                                            ],
                                            'PostalZone' => [
                                                [
                                                    '_' => $eInvoiceHeader->EINV_SUP_POSTCODE ?? '',
                                                ],
                                            ],
                                            'CountrySubentityCode' => [
                                                [
                                                    '_' => $eInvoiceHeader->EINV_SUP_STATE_ID,
                                                ],
                                            ],
                                            'Country' => [
                                                [
                                                    'IdentificationCode' => [
                                                        [
                                                            '_' => $eInvoiceHeader->EINV_SUP_COUNTRY_ID,
                                                            'listID' => 'ISO3166-1',
                                                            'listAgencyID' => '6',
                                                        ],
                                                    ],
                                                ],
                                            ],
                                            'AddressLine' => [
                                                [
                                                    'Line' => [
                                                        [
                                                            '_' => $eInvoiceHeader->EINV_SUP_ADDR0 ?? '',
                                                        ],
                                                    ],
                                                ],
                                                [
                                                    'Line' => [
                                                        [
                                                            '_' => $eInvoiceHeader->EINV_SUP_ADDR1 ?? '',
                                                        ],
                                                    ],
                                                ],
                                                [
                                                    'Line' => [
                                                        [
                                                            '_' => $eInvoiceHeader->EINV_SUP_ADDR2 ?? '',
                                                        ],
                                                    ],
                                                ],
                                            ],

                                        ],
                                    ],
                                    'PartyLegalEntity' => [
                                        [
                                            'RegistrationName' => [
                                                [
                                                    '_' => $eInvoiceHeader->EINV_SUP_NAME,
                                                ],
                                            ],
                                        ],
                                    ],
                                    'Contact' => [
                                        [
                                            'Telephone' => [
                                                [
                                                    '_' => !empty($eInvoiceHeader->EINV_SUP_CONTACT) ? str_replace([' ', '-'], '', $eInvoiceHeader->EINV_SUP_CONTACT) : '-',
                                                ],
                                            ],
                                            'ElectronicMail' => [
                                                [
                                                    '_' => (!empty($eInvoiceHeader->EINV_SUP_EMAIL) && trim($eInvoiceHeader->EINV_SUP_EMAIL) != 'NA') ? $eInvoiceHeader->EINV_SUP_EMAIL : 'noemail@noemail.com',
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'AccountingCustomerParty' => [
                        [
                            'Party' => [
                                [
                                    'PostalAddress' => [
                                        [
                                            'CityName' => [
                                                [
                                                    '_' => $eInvoiceHeader->EINV_BUY_CITY ?? '',
                                                ],
                                            ],
                                            'PostalZone' => [
                                                [
                                                    '_' => $eInvoiceHeader->EINV_BUY_POSTCODE ?? '',
                                                ],
                                            ],
                                            'CountrySubentityCode' => [
                                                [
                                                    '_' => $eInvoiceHeader->EINV_BUY_STATE_ID,
                                                ],
                                            ],
                                            'Country' => [
                                                [
                                                    'IdentificationCode' => [
                                                        [
                                                            '_' => $eInvoiceHeader->EINV_BUY_COUNTRY_ID,
                                                            'listID' => 'ISO3166-1',
                                                            'listAgencyID' => '6',
                                                        ],
                                                    ],
                                                ],
                                            ],
                                            'AddressLine' => [
                                                [
                                                    'Line' => [
                                                        [
                                                            '_' => $eInvoiceHeader->EINV_BUY_ADDR0 ?? '',
                                                        ],
                                                    ],
                                                ],
                                                [
                                                    'Line' => [
                                                        [
                                                            '_' => $eInvoiceHeader->EINV_BUY_ADDR1 ?? '',
                                                        ],
                                                    ],
                                                ],
                                                [
                                                    'Line' => [
                                                        [
                                                            '_' => $eInvoiceHeader->EINV_BUY_ADDR2 ?? '',
                                                        ],
                                                    ],
                                                ],
                                            ],

                                        ],
                                    ],
                                    'PartyLegalEntity' => [
                                        [
                                            'RegistrationName' => [
                                                [
                                                    '_' => $eInvoiceHeader->EINV_BUY_NAME,
                                                ],
                                            ],
                                        ],
                                    ],
                                    'PartyIdentification' => [
                                        [
                                            'ID' => [
                                                [
                                                    '_' => preg_replace('/\s+/', '', $eInvoiceHeader->EINV_BUY_TIN),
                                                    'schemeID' => 'TIN',
                                                ],
                                            ],
                                        ],
                                        [
                                            'ID' => [
                                                [
                                                    '_' => $eInvoiceHeader->EINV_BUY_ROC,
                                                    'schemeID' => $eInvoiceHeader->EINV_BUY_REG_TYPE,
                                                ],
                                            ],
                                        ],
                                        [
                                            'ID' => [
                                                [
                                                    '_' => $eInvoiceHeader->EINV_BUY_SST,
                                                    'schemeID' => 'SST',
                                                ],
                                            ],
                                        ],
                                        [
                                            'ID' => [
                                                [
                                                    '_' => $eInvoiceHeader->EINV_BUY_TTX,
                                                    'schemeID' => 'TTX',
                                                ],
                                            ],
                                        ],
                                    ],
                                    'Contact' => [
                                        [
                                            'Telephone' => [
                                                [
                                                    '_' => !empty($eInvoiceHeader->EINV_BUY_CONTACT) ? str_replace([' ', '-'], '', $eInvoiceHeader->EINV_BUY_CONTACT) : '-',
                                                ],
                                            ],
                                            'ElectronicMail' => [
                                                [
                                                    '_' => (!empty($eInvoiceHeader->EINV_BUY_EMAIL) && trim($eInvoiceHeader->EINV_BUY_EMAIL) != 'NA') ? $eInvoiceHeader->EINV_BUY_EMAIL : 'noemail@noemail.com',
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'Delivery' => [
                        [
                            'DeliveryParty' => [
                                [
                                    'PartyLegalEntity' => [
                                        [
                                            'RegistrationName' => [
                                                [
                                                    '_' => $eInvoiceHeader->EINV_SHIP_RCPT_NAME,
                                                ],
                                            ],
                                        ],
                                    ],
                                    'PostalAddress' => [
                                        [
                                            'CityName' => [
                                                [
                                                    '_' => $eInvoiceHeader->EINV_SHIP_RCPT_CITY,
                                                ],
                                            ],
                                            'PostalZone' => [
                                                [
                                                    '_' => $eInvoiceHeader->EINV_SHIP_RCPT_POSTCODE,
                                                ],
                                            ],
                                            'CountrySubentityCode' => [
                                                [
                                                    '_' => $eInvoiceHeader->EINV_SHIP_RCPT_STATE_ID,
                                                ],
                                            ],
                                            'Country' => [
                                                [
                                                    'IdentificationCode' => [
                                                        [
                                                            '_' => $eInvoiceHeader->EINV_SHIP_RCPT_COUNTRY_ID,
                                                            'listID' => 'ISO3166-1',
                                                            'listAgencyID' => '6',
                                                        ],
                                                    ],
                                                ],
                                            ],
                                            'AddressLine' => [
                                                [
                                                    'Line' => [
                                                        [
                                                            '_' => $eInvoiceHeader->EINV_SHIP_RCPT_ADDR0 ?? '',
                                                        ],
                                                    ],
                                                ],
                                                [
                                                    'Line' => [
                                                        [
                                                            '_' => $eInvoiceHeader->EINV_SHIP_RCPT_ADDR1 ?? '',
                                                        ],
                                                    ],
                                                ],
                                                [
                                                    'Line' => [
                                                        [
                                                            '_' => $eInvoiceHeader->EINV_SHIP_RCPT_ADDR2 ?? '',
                                                        ],
                                                    ],
                                                ],
                                            ],

                                        ],
                                    ],
                                    'PartyIdentification' => [
                                        [
                                            'ID' => [
                                                [
                                                    '_' => preg_replace('/\s+/', '', $eInvoiceHeader->EINV_SHIP_RCPT_TIN),
                                                    'schemeID' => 'TIN',
                                                ],
                                            ],
                                        ],
                                        [
                                            'ID' => [
                                                [
                                                    '_' => $eInvoiceHeader->EINV_SHIP_RCPT_ROC,
                                                    'schemeID' => 'BRN',
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'Shipment' => [
                                [
                                    'ID' => [
                                        [
                                            '_' => $eInvoiceHeader->EINV_ID,
                                        ],
                                    ],
                                    'FreightAllowanceCharge' => [
                                        [
                                            'ChargeIndicator' => [
                                                [
                                                    '_' => true,
                                                ],
                                            ],
                                            'AllowanceChargeReason' => [
                                                [
                                                    '_' => $eInvoiceHeader->EINV_DETAIL_OTHERS_REASON ?? '',
                                                ],
                                            ],
                                            'Amount' => [
                                                [
                                                    '_' => $eInvoiceHeader->EINV_DETAIL_OTHERS_AMT ? floatval($eInvoiceHeader->EINV_DETAIL_OTHERS_AMT) : 0,
                                                    'currencyID' => $currencyCode,
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'PaymentMeans' => [
                        [
                            'PaymentMeansCode' => [
                                [
                                    '_' => $eInvoiceHeader->EINV_PAYMENT_MODE ?? '',
                                ],
                            ],
                            'PayeeFinancialAccount' => [
                                [
                                    'ID' => [
                                        [
                                            '_' => $eInvoiceHeader->EINV_SUP_BANK_ACCT ?? '',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'PaymentTerms' => [
                        [
                            'Note' => [
                                [
                                    '_' => $eInvoiceHeader->EINV_PAYMENT_TERMS ?? '',
                                ],
                            ],
                        ],
                    ],
                    'PrepaidPayment' => [
                        [
                            'ID' => [
                                [
                                    '_' => $eInvoiceHeader->EINV_PREPAYMENT_REF ?? '',
                                ],
                            ],
                            'PaidAmount' => [
                                [
                                    '_' => floatval($eInvoiceHeader->EINV_PREPAYMENT_AMT),
                                    'currencyID' => $currencyCode,
                                ],
                            ],
                            'PaidDate' => [
                                [
                                    '_' => !empty($eInvoiceHeader->EINV_PREPAYMENT_DATE) ? Carbon::parse($eInvoiceHeader->EINV_PREPAYMENT_DATE)->setTimezone(config('services.einvoice.timezone'))->subSeconds(1)->format('Y-m-d') : '',
                                ],
                            ],
                            'PaidTime' => [
                                [
                                    '_' => Carbon::parse($eInvoiceHeader->EINV_PREPAYMENT_DATE)->setTimezone(config('services.einvoice.timezone'))->subSeconds(1)->format('H:i:s') . 'Z',
                                ],
                            ],
                        ],
                    ],
                    'AllowanceCharge' => [
                        [
                            'ChargeIndicator' => [
                                [
                                    '_' => false,
                                ],
                            ],
                            'AllowanceChargeReason' => [
                                [
                                    '_' => $eInvoiceHeader->EINV_TOTAL_ADD_DISC_REASON ?? '',
                                ],
                            ],
                            'Amount' => [
                                [
                                    '_' => $additionalDiscountAmount,
                                    'currencyID' => $currencyCode,
                                ],
                            ],
                        ],
                        [
                            'ChargeIndicator' => [
                                [
                                    '_' => true,
                                ],
                            ],
                            'AllowanceChargeReason' => [
                                [
                                    '_' => $eInvoiceHeader->EINV_TOTAL_ADD_FEE_REASON ?? '',
                                ],
                            ],
                            'Amount' => [
                                [
                                    '_' => $additionalFeeAmount,
                                    'currencyID' => $currencyCode,
                                ],
                            ],
                        ],
                    ],
                    'TaxTotal' => [
                        [
                            'TaxAmount' => [
                                [
                                    '_' => 0,
                                    'currencyID' => $currencyCode,
                                ],
                            ],
                            'TaxSubtotal' => [
                                [
                                    'TaxableAmount' => [
                                        [
                                            '_' => floatval($this->totalTaxableAmtPerTaxType),
                                            'currencyID' => $currencyCode,
                                        ],
                                    ],
                                    'TaxAmount' => [
                                        [
                                            '_' => floatval($this->totalTaxAmtPerTaxType),
                                            'currencyID' => $currencyCode,
                                        ],
                                    ],
                                    'TaxCategory' => [
                                        [
                                            'ID' => [
                                                [
                                                    '_' => $eInvoiceDetail->first()->EINV_TAX_TYPE,
                                                ],
                                            ],
                                            'TaxScheme' => [
                                                [
                                                    'ID' => [
                                                        [
                                                            '_' => 'OTH',
                                                            'schemeID' => 'UN/ECE 5153',
                                                            'schemeAgencyID' => '6',
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'LegalMonetaryTotal' => [
                        [
                            'LineExtensionAmount' => [
                                [
                                    '_' => floatval($this->totalNetAmt),
                                    'currencyID' => $currencyCode,
                                ],
                            ],
                            'TaxExclusiveAmount' => [
                                [
                                    '_' => floatval($this->totalExcTaxAmt) + $additionalDiscountAmount + $additionalFeeAmount,
                                    'currencyID' => $currencyCode,
                                ],
                            ],
                            'TaxInclusiveAmount' => [
                                [
                                    '_' => floatval($this->totalIncTaxAmt) + $additionalDiscountAmount + $additionalFeeAmount,
                                    'currencyID' => $currencyCode,
                                ],
                            ],
                            'AllowanceTotalAmount' => [
                                [
                                    '_' => floatval($this->totalDiscAmt),
                                    'currencyID' => $currencyCode,
                                ],
                            ],
                            'ChargeTotalAmount' => [
                                [
                                    '_' => floatval($this->totalChargeAmt),
                                    'currencyID' => $currencyCode,
                                ],
                            ],
                            'PayableRoundingAmount' => [
                                [
                                    '_' => floatval($eInvoiceHeader->EINV_ROUNDING_AMT),
                                    'currencyID' => $currencyCode,
                                ],
                            ],
                            'PayableAmount' => [
                                [
                                    '_' => (floatval($this->totalPayableAmt) + $additionalDiscountAmount + $additionalFeeAmount + floatval($eInvoiceHeader->EINV_ROUNDING_AMT)) - floatval($eInvoiceHeader->EINV_PREPAYMENT_AMT),
                                    'currencyID' => $currencyCode,
                                ],
                            ],
                        ],
                    ],
                    'InvoiceLine' => $invoiceLines,
                ],
            ],
        ];

        if ($currencyCode != 'MYR') {
            $data = $this->addTaxEchangeRate($data, $currencyCode, $eInvoiceHeader);
        }

        return $data;
    }

    private function populateInvoiceLines(Collection $eInvoiceDetail, EinvoiceHeader $eInvoiceHeader)
    {
        foreach ($eInvoiceDetail as $dt) {
            $this->totalExcTaxAmt += floatval($dt->EINV_TOTAL_EXCL_TAX);
            $this->totalIncTaxAmt = $this->totalIncTaxAmt + floatval($dt->EINV_TOTAL_INCL_TAX) + floatval($dt->EINV_TAX_AMT);
            $this->totalPayableAmt = $this->totalPayableAmt + floatval($dt->EINV_TOTAL_PAYABLE_AMT) + floatval($dt->EINV_TAX_AMT);
            $this->totalNetAmt += floatval($dt->EINV_TOTAL_NET_AMT);
            $this->totalDiscAmt += floatval($dt->EINV_DISC_AMT);
            $this->totalChargeAmt += floatval($dt->EINV_FEE_AMT);

            $this->totalTaxableAmtPerTaxType += floatval($dt->EINV_TOTAL_EXCL_TAX) - floatval($dt->EINV_TAX_AMT_EXEMPTED);
            $this->totalTaxAmtPerTaxType = floatval($dt->EINV_TAX_AMT);
            $invoiceLines[] = [
                'ID' => [
                    [
                        '_' => strval($dt->EINV_SEQ),
                    ],
                ],
                'InvoicedQuantity' => [
                    [
                        '_' => intval($dt->EINV_QTY),
                        'unitCode' => $dt->EINV_UOM_ID ?? '',
                    ],
                ],
                'LineExtensionAmount' => [
                    [
                        '_' => floatval($dt->EINV_TOTAL_EXCL_TAX),
                        'currencyID' => $eInvoiceHeader->EINV_CURR,
                    ],
                ],
                'AllowanceCharge' => [
                    [
                        'ChargeIndicator' => [
                            [
                                '_' => false,
                            ],
                        ],
                        'AllowanceChargeReason' => [
                            [
                                '_' => '',
                            ],
                        ],
                        'MultiplierFactorNumeric' => [
                            [
                                '_' => round($dt->EINV_DISC_RATE, 2),
                            ],
                        ],
                        'Amount' => [
                            [
                                '_' => floatval($dt->EINV_DISC_AMT),
                                'currencyID' => $eInvoiceHeader->EINV_CURR,
                            ],
                        ],
                    ],
                    [
                        'ChargeIndicator' => [
                            [
                                '_' => true,
                            ],
                        ],
                        'AllowanceChargeReason' => [
                            [
                                '_' => '',
                            ],
                        ],
                        'MultiplierFactorNumeric' => [
                            [
                                '_' => round($dt->EINV_FEE_RATE, 2),
                            ],
                        ],
                        'Amount' => [
                            [
                                '_' => floatval(value: $dt->EINV_FEE_AMT),
                                'currencyID' => $eInvoiceHeader->EINV_CURR,
                            ],
                        ],
                    ],
                ],
                'TaxTotal' => [
                    [
                        'TaxAmount' => [
                            [
                                '_' => floatval($dt->EINV_TAX_AMT),
                                'currencyID' => $eInvoiceHeader->EINV_CURR,
                            ],
                        ],
                        'TaxSubtotal' => [
                            [
                                'TaxableAmount' => [
                                    [
                                        '_' => floatval($dt->EINV_TAX_AMT_EXEMPTED),
                                        'currencyID' => $eInvoiceHeader->EINV_CURR,
                                    ],
                                ],
                                'TaxAmount' => [
                                    [
                                        '_' => floatval($dt->EINV_TAX_AMT),
                                        'currencyID' => $eInvoiceHeader->EINV_CURR,
                                    ],
                                ],
                                'Percent' => [
                                    [
                                        '_' => floor($dt->EINV_TAX_RATE),
                                    ],
                                ],
                                'TaxCategory' => [
                                    [
                                        'ID' => [
                                            [
                                                '_' => $dt->EINV_TAX_TYPE,
                                            ],
                                        ],
                                        'TaxExemptionReason' => [
                                            [
                                                '_' => $dt->EINV_TAX_EXEMPTION_DESC ?? '',
                                            ],
                                        ],
                                        'TaxScheme' => [
                                            [
                                                'ID' => [
                                                    [
                                                        '_' => 'OTH',
                                                        'schemeID' => 'UN/ECE 5153',
                                                        'schemeAgencyID' => '6',
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'Item' => [
                    [
                        'CommodityClassification' => [
                            [
                                'ItemClassificationCode' => [
                                    [
                                        '_' => $dt->EINV_PROD_TARIFF_CODE ?? '',
                                        'listID' => 'PTC',
                                    ],
                                ],
                            ],
                            [
                                'ItemClassificationCode' => [
                                    [
                                        '_' => $dt->EINV_CLASSIFICATION,
                                        'listID' => 'CLASS',
                                    ],
                                ],
                            ],
                        ],
                        'Description' => [
                            [
                                '_' => $dt->EINV_PRODUCT_DESC,
                            ],
                        ],
                        'OriginCountry' => [
                            [
                                'IdentificationCode' => [
                                    [
                                        '_' => $dt->EINV_COUNTRY_OF_ORI ?? '',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'Price' => [
                    [
                        'PriceAmount' => [
                            [
                                '_' => floatval($dt->EINV_NETT_UNIT_PRICE),
                                'currencyID' => $eInvoiceHeader->EINV_CURR,
                            ],
                        ],
                    ],
                ],
                'ItemPriceExtension' => [
                    [
                        'Amount' => [
                            [
                                '_' => floatval($dt->EINV_SUBTOTAL),
                                'currencyID' => $eInvoiceHeader->EINV_CURR,
                            ],
                        ],
                    ],
                ],
            ];
        }
        return $invoiceLines;
    }

    private function populateBillingRefence(EinvoiceHeader $eInvoiceHeader, DocumentType $documentType)
    {
        $billingReference = [];
        if ($documentType == DocumentType::CREDIT_NOTE || $documentType == DocumentType::DEBIT_NOTE || $documentType == DocumentType::SUPPLIER_CREDIT_NOTE || $documentType == DocumentType::SUPPLIER_DEBIT_NOTE) {
            if (!empty($eInvoiceHeader->EINV_UIN_REF_JSON)) {
                $billingReferencesInformations = json_decode($eInvoiceHeader->EINV_UIN_REF_JSON, true);
                foreach ($billingReferencesInformations as $index => $info) {
                    $billingReference[]['InvoiceDocumentReference'][0] = [
                        'ID' => [
                            [
                                '_' => $info['id'] ?? 'NA'
                            ],
                        ],
                        'UUID' => [
                            [
                                '_' => $info['uuid'] ?? 'NA',
                            ],
                        ],
                    ];
                }
            } else {
                $billingReference[]['InvoiceDocumentReference'][0] = [
                    'ID' => [
                        [
                            '_' => 'NA'
                        ],
                    ],
                    'UUID' => [
                        [
                            '_' => 'NA',
                        ],
                    ],
                ];
            }



        }
        $billingReference[]['AdditionalDocumentReference'][0] = [
            'ID' => [
                [
                    '_' => $eInvoiceHeader->EINV_BILL_REF ?? '',
                ],
            ],
        ];
        return $billingReference;
    }

    public function addTaxEchangeRate(array $data, $currencyCode, EinvoiceHeader $eInvoiceHeader)
    {
        $data['Invoice'][0]['TaxExchangeRate'][0]['SourceCurrencyCode'][0]['_'] = $currencyCode;
        $data['Invoice'][0]['TaxExchangeRate'][0]['TargetCurrencyCode'][0]['_'] = 'MYR';
        $data['Invoice'][0]['TaxExchangeRate'][0]['CalculationRate'][0]['_'] = floatval($eInvoiceHeader->EINV_CURR_RATE);

        return $data;
    }
}
