<?php

namespace App\Services\EInvoice;
use App\Models\ManualIssueEinvoiceHeader;
use App\Models\ManualIssueEinvoiceDetail;
use Log;
class ManualIssueDocumentProcessor
{
    /**
     * Create a new service instance.
     *
     * @return void
     */

    public function __construct(protected $document)
    {
        //
    }

    /**
     * Example method for the service.
     *
     * @param  string $message
     * @return string
     */
    public function isJson(): bool
    {
        json_decode($this->document);
        $isJson = (json_last_error() === JSON_ERROR_NONE);
        return $isJson;

    }
    protected function getValue(array $data, string $key = '_')
    {
        Log::info($data[0]);
        return $data[0][$key];
    }
    public function processJsonDocument(): void
    {
        $data = json_decode($this->document, true);
        $invoice = $data['Invoice'][0];
        $billingReference = $invoice['BillingReference'];
        $addionalDocumentReference = $invoice['AdditionalDocumentReference'];
        $accountingSupplierParty = $invoice['AccountingSupplierParty'][0];
        $supplierTin = null;
        $supplierRegType = null;
        $supplierRoc = null;
        $supplierSst = null;
        $supplierTtx = null;
        // Log::info($accountingSupplierParty);
        foreach ($accountingSupplierParty['Party'][0]['PartyIdentification'] as $supplierIdentification) {
            $schemeId = $supplierIdentification['ID'][0]['schemeID'];
            $value = $supplierIdentification['ID'][0]['_'];
            if ($schemeId == 'TIN') {
                $supplierTin = $value;
                return;
            }

            if ($schemeId == 'SST') {
                $supplierSst = $value;
                return;
            }

            if ($schemeId == 'TTX') {
                $supplierTtx = $value;
                return;
            }
            $supplierRegType = $schemeId;
            $supplierRoc = $value;
        }
        return;
        ManualIssueEinvoiceHeader::create([
            'EINV_ID' => $this->getValue($invoice['ID']),
            'EINV_ISSUE_DATE' => $this->getValue($invoice['IssueDate']),
            'EINV_ISSUE_TIME' => $this->getValue($invoice['IssueTime']),
            'EINV_TYPE' => $this->getValue($invoice['InvoiceTypeCode']),
            'EINV_VERSION' => $this->getValue($invoice['InvoiceTypeCode'], "listVersionID"),
            'EINV_CURR' => $this->getValue($invoice['DocumentCurrencyCode']),
            'EINV_TAX_CURR' => $this->getValue($invoice['TaxCurrencyCode']),
            'EINV_FREQ' => $this->getValue($invoice['InvoicePeriod'][0]['Description']),
            'EINV_REF_CUSTOM' => $this->getValue($addionalDocumentReference[0]['ID']),
            'EINV_FTA' => $this->getValue($addionalDocumentReference[1]['ID']),
            'EINV_REF_CUSTOM_2' => $this->getValue($addionalDocumentReference[2]['ID']),
            'EINV_INCOTERMS' => $this->getValue($addionalDocumentReference[3]['ID']),
            'EINV_AUTH_CERT' => $this->getValue($accountingSupplierParty['AdditionalAccountID']),
            'EINV_SUP_MSIC' => $this->getValue($accountingSupplierParty['Party'][0]['IndustryClassificationCode']),
            'EINV_SUP_BUS_ACT_DESC' => $this->getValue($accountingSupplierParty['Party'][0]['IndustryClassificationCode'], "name"),
        ]);
    }

    public function processXmlDocument()
    {

    }
}