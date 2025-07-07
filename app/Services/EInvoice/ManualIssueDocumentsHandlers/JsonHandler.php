<?php
namespace App\Services\EInvoice\ManualIssueDocumentsHandlers;

use App\Services\GeneralService;
use Carbon\Carbon;
use App\Models\ManualIssueEinvoiceHeader;
use App\Models\ManualIssueEinvoiceDetail;
use App\Models\EinvoiceState;
use App\Models\EinvoiceCountry;
class JsonHandler
{
    protected $generalService;
    protected $einvoiceState;
    protected $einvoiceCountry;
    public function __construct(protected $information)
    {
        $this->generalService = new GeneralService();
    }

    protected function getTaxTotalData($taxTotal)
    {
        return [
            'tax_amount' => $this->generalService->getEInvoiceArrayValue($taxTotal['TaxAmount']),
            'total_taxable_amount' => $this->generalService->getEInvoiceArrayValue($taxTotal['TaxSubtotal'][0]['TaxableAmount']),
            'total_tax_amount' => $this->generalService->getEInvoiceArrayValue($taxTotal['TaxSubtotal'][0]['TaxAmount']),
            'tax_type' => $this->generalService->getEInvoiceArrayValue($taxTotal['TaxSubtotal'][0]['TaxCategory'][0]['ID']),
            'percent' => isset($taxTotal['TaxSubtotal'][0]['Percent']) ? $this->generalService->getEInvoiceArrayValue($taxTotal['TaxSubtotal'][0]['Percent']) : null,
            'tax_exemption_reason' => isset($taxTotal['TaxSubtotal'][0]['TaxCategory'][0]['TaxExemptionReason']) ? $this->generalService->getEInvoiceArrayValue($taxTotal['TaxSubtotal'][0]['TaxCategory'][0]['TaxExemptionReason']) : null,
        ];
    }
    protected function getAllowanceChargeData($allowanceCharge)
    {
        $allowanceChargeData = [
            'discount' => [
                'reason' => null,
                'amount' => null,
            ],
            'fee' => [
                'reason' => null,
                'amount' => null,
            ]
        ];
        foreach ($allowanceCharge as $charge) {
            $indicator = $charge['ChargeIndicator'][0]['_'];
            $handler = &$allowanceChargeData['discount'];
            if ($indicator) {
                $handler = &$allowanceChargeData['fee'];
            }
            $handler['reason'] = $this->generalService->getEInvoiceArrayValue($charge['AllowanceChargeReason']);
            $handler['amount'] = $this->generalService->getEInvoiceArrayValue($charge['Amount']);
        }
        return $allowanceChargeData;
    }

    protected function getIdentificationData($identityData, &$data)
    {
        foreach ($identityData as $index => $identity) {
            $schemeId = $identity['ID'][0]['schemeID'];
            $value = $identity['ID'][0]['_'];
            if (in_array($schemeId, ['TIN', 'SST', 'TTX'])) {
                $data[strtolower($schemeId)] = $value;
            } else {
                $data['registration_type'] = $schemeId;
                $data['roc'] = $value;
            }
        }
    }
    protected function getDeliveryData($delivery): array
    {
        $deliveryData = [
            'name' => $this->generalService->getEInvoiceArrayValue($delivery['DeliveryParty'][0]['PartyLegalEntity'][0]['RegistrationName']),
            'city' => $this->generalService->getEInvoiceArrayValue($delivery['DeliveryParty'][0]['PostalAddress'][0]['CityName']),
            'postcode' => $this->generalService->getEInvoiceArrayValue($delivery['DeliveryParty'][0]['PostalAddress'][0]['PostalZone']),
            'state' => $this->generalService->getEInvoiceArrayValue($delivery['DeliveryParty'][0]['PostalAddress'][0]['CountrySubentityCode']),
            'country' => $this->generalService->getEInvoiceArrayValue($delivery['DeliveryParty'][0]['PostalAddress'][0]['Country'][0]['IdentificationCode']),
            'addr0' => $this->generalService->getEInvoiceArrayValue($delivery['DeliveryParty'][0]['PostalAddress'][0]['AddressLine'][0]['Line']),
            'addr1' => $this->generalService->getEInvoiceArrayValue($delivery['DeliveryParty'][0]['PostalAddress'][0]['AddressLine'][1]['Line']),
            'addr2' => $this->generalService->getEInvoiceArrayValue($delivery['DeliveryParty'][0]['PostalAddress'][0]['AddressLine'][2]['Line']),
            'tin' => null,
            'roc' => null,
            'registration_type' => null,
            'other_detail_reason' => $this->generalService->getEInvoiceArrayValue($delivery['Shipment'][0]['FreightAllowanceCharge'][0]['AllowanceChargeReason']),
            'other_detail_amount' => $this->generalService->getEInvoiceArrayValue($delivery['Shipment'][0]['FreightAllowanceCharge'][0]['Amount']),
        ];
        $this->getIdentificationData($delivery['DeliveryParty'][0]['PartyIdentification'], $deliveryData);
        return $deliveryData;
    }

    protected function getCustomerData($accountingCustomerParty): array
    {
        $customerData = [
            'tin' => null,
            'sst' => null,
            'ttx' => null,
            'registration_type' => null,
            'roc' => null,
            'city' => $this->generalService->getEInvoiceArrayValue($accountingCustomerParty['Party'][0]['PostalAddress'][0]['CityName']),
            'postcode' => $this->generalService->getEInvoiceArrayValue($accountingCustomerParty['Party'][0]['PostalAddress'][0]['PostalZone']),
            'state' => $this->generalService->getEInvoiceArrayValue($accountingCustomerParty['Party'][0]['PostalAddress'][0]['CountrySubentityCode']),
            'country' => $this->generalService->getEInvoiceArrayValue($accountingCustomerParty['Party'][0]['PostalAddress'][0]['Country'][0]['IdentificationCode']),
            'addr0' => $this->generalService->getEInvoiceArrayValue($accountingCustomerParty['Party'][0]['PostalAddress'][0]['AddressLine'][0]['Line']),
            'addr1' => $this->generalService->getEInvoiceArrayValue($accountingCustomerParty['Party'][0]['PostalAddress'][0]['AddressLine'][1]['Line']),
            'addr2' => $this->generalService->getEInvoiceArrayValue($accountingCustomerParty['Party'][0]['PostalAddress'][0]['AddressLine'][2]['Line']),
            'name' => $this->generalService->getEInvoiceArrayValue($accountingCustomerParty['Party'][0]['PartyLegalEntity'][0]['RegistrationName']),
            'contact' => $this->generalService->getEInvoiceArrayValue($accountingCustomerParty['Party'][0]['Contact'][0]['Telephone']),
            'email' => $this->generalService->getEInvoiceArrayValue($accountingCustomerParty['Party'][0]['Contact'][0]['ElectronicMail']),
        ];
        $this->getIdentificationData(($accountingCustomerParty['Party'][0]['PartyIdentification']), $customerData);
        return $customerData;
    }
    protected function getSupplierData($accountingSupplierParty): array
    {
        $supplierData = [
            'cert' => $this->generalService->getEInvoiceArrayValue($accountingSupplierParty['AdditionalAccountID']),
            'classification_code' => $this->generalService->getEInvoiceArrayValue($accountingSupplierParty['Party'][0]['IndustryClassificationCode']),
            'classification_name' => $this->generalService->getEInvoiceArrayValue($accountingSupplierParty['Party'][0]['IndustryClassificationCode'], 'name'),
            'tin' => null,
            'sst' => null,
            'ttx' => null,
            'registration_type' => null,
            'roc' => null,
            'city' => $this->generalService->getEInvoiceArrayValue($accountingSupplierParty['Party'][0]['PostalAddress'][0]['CityName']),
            'postcode' => $this->generalService->getEInvoiceArrayValue($accountingSupplierParty['Party'][0]['PostalAddress'][0]['PostalZone']),
            'state' => $this->generalService->getEInvoiceArrayValue($accountingSupplierParty['Party'][0]['PostalAddress'][0]['CountrySubentityCode']),
            'country' => $this->generalService->getEInvoiceArrayValue($accountingSupplierParty['Party'][0]['PostalAddress'][0]['Country'][0]['IdentificationCode']),
            'addr0' => $this->generalService->getEInvoiceArrayValue($accountingSupplierParty['Party'][0]['PostalAddress'][0]['AddressLine'][0]['Line']),
            'addr1' => $this->generalService->getEInvoiceArrayValue($accountingSupplierParty['Party'][0]['PostalAddress'][0]['AddressLine'][1]['Line']),
            'addr2' => $this->generalService->getEInvoiceArrayValue($accountingSupplierParty['Party'][0]['PostalAddress'][0]['AddressLine'][2]['Line']),
            'name' => $this->generalService->getEInvoiceArrayValue($accountingSupplierParty['Party'][0]['PartyLegalEntity'][0]['RegistrationName']),
            'contact' => $this->generalService->getEInvoiceArrayValue($accountingSupplierParty['Party'][0]['Contact'][0]['Telephone']),
            'email' => $this->generalService->getEInvoiceArrayValue($accountingSupplierParty['Party'][0]['Contact'][0]['ElectronicMail']),
        ];
        $this->getIdentificationData(($accountingSupplierParty['Party'][0]['PartyIdentification']), $supplierData);
        return $supplierData;
    }

    public function setState($state)
    {
        $this->einvoiceState = EinvoiceState::whereIn('EINV_STATE_CODE', $state)->get();
    }
    public function getStateName($state)
    {
        return optional($this->einvoiceState->where('EINV_STATE_CODE', $state)->where('EINV_STATE_CODE', '<>', '17')->first())->EINV_STATE_NAME ?? '';
    }

    public function setCountry($country)
    {
        $this->einvoiceCountry = EinvoiceCountry::whereIn('EINV_COUNTRY_CODE', $country)->get();
    }
    public function getCountryName($country)
    {
        return optional($this->einvoiceCountry->where('EINV_STATE_CODE', $country)->first())->EINV_COUNTRY_NAME ?? '';
    }
    public function getInvoiceLineData($invoicesLine, $id)
    {
        $invoicesLineData = [];
        foreach ($invoicesLine as $invoiceLine) {
            $allowanceChargeData = [
                'discount' => [
                    'rate' => null,
                    'amount' => null,
                    'reason' => null,
                ],
                'fee' => [
                    'rate' => null,
                    'amount' => null,
                    'reason' => null,
                ]
            ];
            foreach ($invoiceLine['AllowanceCharge'] as $allowanceCharge) {
                $indicator = $this->generalService->getEInvoiceArrayValue($allowanceCharge['ChargeIndicator']);
                $handler = &$allowanceChargeData['discount'];
                if ($indicator) {
                    $handler = &$allowanceChargeData['fee'];
                }
                $handler['reason'] = $this->generalService->getEInvoiceArrayValue($allowanceCharge['AllowanceChargeReason']);
                $handler['rate'] = $this->generalService->getEInvoiceArrayValue($allowanceCharge['MultiplierFactorNumeric']);
                $handler['amount'] = $this->generalService->getEInvoiceArrayValue($allowanceCharge['Amount']);
                unset($handler);
            }

            $taxTotalData = $this->getTaxTotalData($invoiceLine['TaxTotal'][0]);
            $tarrifCode = null;
            $classification = null;
            foreach ($invoiceLine['Item'][0]['CommodityClassification'] as $commodityClassification) {
                $listId = $this->generalService->getEInvoiceArrayValue($commodityClassification['ItemClassificationCode'], 'listID');
                $value = $this->generalService->getEInvoiceArrayValue($commodityClassification['ItemClassificationCode']);
                if ($listId == 'PTC') {
                    $tarrifCode = $value;
                } else {
                    $classification = $value;
                }
            }
            $invoicesLineData[] = [
                'EINV_ID' => $id,
                'EINV_SEQ' => $this->generalService->getEInvoiceArrayValue($invoiceLine['ID']),
                'EINV_QTY' => $this->generalService->getEInvoiceArrayValue($invoiceLine['InvoicedQuantity']),
                'EINV_UOM_ID' => $this->generalService->getEInvoiceArrayValue($invoiceLine['InvoicedQuantity'], 'unitCode'),
                'EINV_TOTAL_EXCL_TAX' => $this->generalService->getEInvoiceArrayValue($invoiceLine['LineExtensionAmount']),
                'EINV_DISC_REASON' => $allowanceChargeData['discount']['reason'],
                'EINV_DISC_RATE' => $allowanceChargeData['discount']['rate'],
                'EINV_DISC_AMT' => $allowanceChargeData['discount']['amount'],
                'EINV_FEE_REASON' => $allowanceChargeData['fee']['reason'],
                'EINV_FEE_RATE' => $allowanceChargeData['fee']['rate'],
                'EINV_FEE_AMT' => $allowanceChargeData['fee']['amount'],
                'EINV_TAX_AMT' => $taxTotalData['total_tax_amount'],
                'EINV_TAX_AMT_EXEMPTED' => $taxTotalData['total_taxable_amount'],
                'EINV_TAX_RATE' => $taxTotalData['percent'],
                'EINV_TAX_TYPE' => $taxTotalData['tax_type'],
                'EINV_TAX_EXEMPTION_DESC' => $taxTotalData['tax_exemption_reason'],
                'EINV_PROD_TARIFF_CODE' => $tarrifCode,
                'EINV_CLASSIFICATION' => $classification,
                'EINV_PRODUCT_DESC' => $this->generalService->getEInvoiceArrayValue($invoiceLine['Item'][0]['Description']),
                'EINV_COUNTRY_OF_ORI' => $this->generalService->getEInvoiceArrayValue($invoiceLine['Item'][0]['OriginCountry'][0]['IdentificationCode']),
                'EINV_NETT_UNIT_PRICE' => $this->generalService->getEInvoiceArrayValue($invoiceLine['Price'][0]['PriceAmount']),
                'EINV_SUBTOTAL' => $this->generalService->getEInvoiceArrayValue($invoiceLine['ItemPriceExtension'][0]['Amount']),
                'EINV_CREATE_BY' => config('constants.SYSTEM_USER_ID'),
                'EINV_CREATE_DATE' => Carbon::now(),
                'EINV_UPD_BY' => config('constants.SYSTEM_USER_ID'),
                'EINV_UPD_DATE' => Carbon::now()
            ];
        }
        return $invoicesLineData;
    }
    public function process()
    {
        $data = json_decode($this->information['document'], true);
        $invoice = $data['Invoice'][0];
        $addionalDocumentReference = $invoice['AdditionalDocumentReference'];
        $supplierData = $this->getSupplierData($invoice['AccountingSupplierParty'][0]);
        $customerData = $this->getCustomerData($invoice['AccountingCustomerParty'][0]);
        $deliveryData = $this->getDeliveryData($invoice['Delivery'][0]);
        $allowanceChargeData = $this->getAllowanceChargeData($invoice['AllowanceCharge']);
        $stateArray = array_values(array_filter(array_unique([$supplierData['state'], $customerData['state'], $deliveryData['state']])));
        $countryArray = array_values(array_filter(array_unique([$supplierData['country'], $customerData['country'], $deliveryData['country']])));
        $legalMonetaryTotal = $invoice['LegalMonetaryTotal'][0];
        $taxTotal = $invoice['TaxTotal'][0];
        $this->setState($stateArray);
        $this->setCountry($countryArray);
        $data = [
            'EINV_ID' => $this->generalService->getEInvoiceArrayValue($invoice['ID']),
            'EINV_ISSUE_DATE' => Carbon::parse($this->generalService->getEInvoiceArrayValue($invoice['IssueDate']) . " " . $this->generalService->getEInvoiceArrayValue($invoice['IssueTime']), config('services.einvoice.timezone'))->setTimezone(config('app.timezone')),
            'EINV_TYPE' => $this->generalService->getEInvoiceArrayValue($invoice['InvoiceTypeCode']),
            'EINV_VERSION' => $this->generalService->getEInvoiceArrayValue($invoice['InvoiceTypeCode'], "listVersionID"),
            'EINV_CURR' => $this->generalService->getEInvoiceArrayValue($invoice['DocumentCurrencyCode']),
            'EINV_TAX_CURR' => isset($invoice['TaxCurrencyCode']) ? $this->generalService->getEInvoiceArrayValue($invoice['TaxCurrencyCode']) : '',
            'EINV_FREQ' => $this->generalService->getEInvoiceArrayValue($invoice['InvoicePeriod'][0]['Description']),
            'EINV_REF_CUSTOM' => $this->generalService->getEInvoiceArrayValue($addionalDocumentReference[0]['ID']),
            'EINV_FTA' => $this->generalService->getEInvoiceArrayValue($addionalDocumentReference[1]['ID']),
            'EINV_REF_CUSTOM_2' => $this->generalService->getEInvoiceArrayValue($addionalDocumentReference[2]['ID']),
            'EINV_INCOTERMS' => $this->generalService->getEInvoiceArrayValue($addionalDocumentReference[3]['ID']),
            'EINV_AUTH_CERT' => $supplierData['cert'],
            'EINV_SUP_MSIC' => $supplierData['classification_code'],
            'EINV_SUP_BUS_ACT_DESC' => $supplierData['classification_name'],
            'EINV_SUP_TIN' => $supplierData['tin'],
            'EINV_SUP_ROC' => $supplierData['roc'],
            'EINV_SUP_REG_TYPE' => $supplierData['registration_type'],
            'EINV_SUP_SST' => $supplierData['sst'],
            'EINV_SUP_TTX' => $supplierData['ttx'],
            'EINV_SUP_CITY' => $supplierData['city'],
            'EINV_SUP_POSTCODE' => $supplierData['postcode'],
            'EINV_SUP_STATE_ID' => $supplierData['state'],
            'EINV_SUP_COUNTRY_ID' => $supplierData['country'],
            'EINV_SUP_ADDR' => $supplierData['addr0'] . $supplierData['addr1'] . $supplierData['addr2'] . " " . $supplierData['postcode'] . " " . $supplierData['city'] . " " . $this->getStateName($supplierData['state']) . " " . $supplierData['country'],
            'EINV_SUP_ADDR0' => $supplierData['addr0'],
            'EINV_SUP_ADDR1' => $supplierData['addr1'],
            'EINV_SUP_ADDR2' => $supplierData['addr2'],
            'EINV_SUP_NAME' => $supplierData['name'],
            'EINV_SUP_CONTACT' => $supplierData['contact'],
            'EINV_SUP_EMAIL' => $supplierData['email'],
            'EINV_BUY_CITY' => $customerData['city'],
            'EINV_BUY_POSTCODE' => $customerData['postcode'],
            'EINV_BUY_STATE_ID' => $customerData['state'],
            'EINV_BUY_COUNTRY_ID' => $customerData['country'],
            'EINV_BUY_ADDR' => $customerData['addr0'] . $customerData['addr1'] . $customerData['addr2'] . $customerData['postcode'] . " " . $customerData['city'] . " " . $this->getStateName($customerData['state']) . " " . $customerData['country'],
            'EINV_BUY_ADDR0' => $customerData['addr0'],
            'EINV_BUY_ADDR1' => $customerData['addr1'],
            'EINV_BUY_ADDR2' => $customerData['addr2'],
            'EINV_BUY_NAME' => $customerData['name'],
            'EINV_BUY_TIN' => $customerData['tin'],
            'EINV_BUY_ROC' => $customerData['roc'],
            'EINV_BUY_REG_TYPE' => $customerData['registration_type'],
            'EINV_BUY_SST' => $customerData['sst'],
            'EINV_BUY_TTX' => $customerData['ttx'],
            'EINV_BUY_CONTACT' => $customerData['contact'],
            'EINV_BUY_EMAIL' => $customerData['email'],
            'EINV_SHIP_RCPT_NAME' => $deliveryData['name'],
            'EINV_SHIP_RCPT_CITY' => $deliveryData['city'],
            'EINV_SHIP_RCPT_POSTCODE' => $deliveryData['postcode'],
            'EINV_SHIP_RCPT_STATE_ID' => $deliveryData['state'],
            'EINV_SHIP_RCPT_COUNTRY_ID' => $deliveryData['country'],
            'EINV_SHIP_RCPT_ADDR' => $deliveryData['addr0'] . $deliveryData['addr1'] . $deliveryData['addr2'] . " " . $deliveryData['postcode'] . " " . $this->getStateName($deliveryData['state']) . " " . $deliveryData['country'],
            'EINV_SHIP_RCPT_ADDR0' => $deliveryData['addr0'],
            'EINV_SHIP_RCPT_ADDR1' => $deliveryData['addr1'],
            'EINV_SHIP_RCPT_ADDR2' => $deliveryData['addr2'],
            'EINV_SHIP_RCPT_TIN' => $deliveryData['tin'],
            'EINV_SHIP_RCPT_ROC' => $deliveryData['roc'],
            'EINV_DETAIL_OTHERS_REASON' => $deliveryData['other_detail_reason'],
            'EINV_DETAIL_OTHERS_AMT' => $deliveryData['other_detail_amount'],
            'EINV_PAYMENT_MODE' => $this->generalService->getEInvoiceArrayValue($invoice['PaymentMeans'][0]['PaymentMeansCode']),
            'EINV_SUP_BANK_ACCT' => $this->generalService->getEInvoiceArrayValue($invoice['PaymentMeans'][0]['PayeeFinancialAccount'][0]['ID']),
            'EINV_PAYMENT_TERMS' => $this->generalService->getEInvoiceArrayValue($invoice['PaymentTerms'][0]['Note']),
            'EINV_PREPAYMENT_REF' => $this->generalService->getEInvoiceArrayValue($invoice['PrepaidPayment'][0]['ID']),
            'EINV_PREPAYMENT_AMT' => $this->generalService->getEInvoiceArrayValue($invoice['PrepaidPayment'][0]['PaidAmount']),
            'EINV_PREPAYMENT_DATE' => !empty($this->generalService->getEInvoiceArrayValue($invoice['PrepaidPayment'][0]['PaidDate'])) ? Carbon::parse($this->generalService->getEInvoiceArrayValue($invoice['PrepaidPayment'][0]['PaidDate']) . " " . $this->generalService->getEInvoiceArrayValue($invoice['PrepaidPayment'][0]['PaidDate']), config('services.einvoice.timezone'))->setTimezone(config('app.timezone')) : null,
            'EINV_TOTAL_ADD_DISC_REASON' => $allowanceChargeData['discount']['reason'],
            'EINV_TOTAL_ADD_DISC_AMT' => $allowanceChargeData['discount']['amount'],
            'EINV_TOTAL_ADD_FEE_REASON' => $allowanceChargeData['fee']['reason'],
            'EINV_TOTAL_ADD_FEE_AMT' => $allowanceChargeData['fee']['amount'],
            'EINV_ROUNDING_AMT' => $this->generalService->getEInvoiceArrayValue($legalMonetaryTotal['PayableRoundingAmount']),
            'EINV_VALIDATE_UUID' => $this->information['uuid'],
            'EINV_SUBMISSION_UID' => $this->information['submissionUid'],
            'EINV_VALIDATE_STATUS' => $this->information['status'],
            'EINV_OVERALL_STATUS' => $this->information['status'],
            'EINV_CANCEL_DATETIME' => !empty($this->information['cancelDateTime']) ? Carbon::parse($this->information['cancelDateTime'], config('services.einvoice.timezone'))->setTimeZone(config('app.timezone')) : null,
            'EINV_REJECT_REQ_DATETIME' => !empty($this->information['rejectRequestDateTime']) ? Carbon::parse($this->information['rejectRequestDateTime'], config('services.einvoice.timezone'))->setTimeZone(config('app.timezone')) : null,
            'EINV_DOC_STATUS_REASON' => $this->information['documentStatusReason'],
            'EINV_VALIDATE_DATETIME' => Carbon::parse($this->information['dateTimeValidated'], config('services.einvoice.timezone'))->setTimezone(config('app.timezone')),
            'EINV_VALIDATE_LINK' => config('services.einvoice.qrcode_base_url') . '/' . $this->information['uuid'] . '/share/' . $this->information['longID'],
            'EINV_DOCUMENT' => $this->information['document'],
            'EINV_SOURCE_CURR' => $this->generalService->getEInvoiceArrayValue($invoice['TaxExchangeRate'][0]['SourceCurrencyCode']),
            'EINV_TARGET_CURR' => $this->generalService->getEInvoiceArrayValue($invoice['TaxExchangeRate'][0]['TargetCurrencyCode']),
            'EINV_CURR_RATE' => $this->generalService->getEInvoiceArrayValue($invoice['TaxExchangeRate'][0]['CalculationRate']),
            'EINV_DATE_TIME' => Carbon::parse($this->generalService->getEInvoiceArrayValue($invoice['IssueDate']) . " " . $this->generalService->getEInvoiceArrayValue($invoice['IssueTime']), config('services.einvoice.timezone'))->setTimezone(config('app.timezone')),
            'EINV_TOTAL_NET_AMT' => $this->generalService->getEInvoiceArrayValue($legalMonetaryTotal['LineExtensionAmount']),
            'EINV_TOTAL_TAX_EXCLUSIVE_AMT' => $this->generalService->getEInvoiceArrayValue($legalMonetaryTotal['TaxExclusiveAmount']),
            'EINV_TOTAL_TAX_INCLUSIVE_AMT' => $this->generalService->getEInvoiceArrayValue($legalMonetaryTotal['TaxInclusiveAmount']),
            'EINV_TOTAL_DISCOUNT_AMT' => $this->generalService->getEInvoiceArrayValue($legalMonetaryTotal['AllowanceTotalAmount']),
            'EINV_TOTAL_CHARGE_AMT' => $this->generalService->getEInvoiceArrayValue($legalMonetaryTotal['ChargeTotalAmount']),
            'EINV_TOTAL_PAYABLE_AMT' => $this->generalService->getEInvoiceArrayValue($legalMonetaryTotal['PayableAmount']),
            'EINV_TOTAL_TAXABLE_AMT' => $this->generalService->getEInvoiceArrayValue($taxTotal['TaxSubtotal'][0]['TaxableAmount']),
            'EINV_TOTAL_TAX_AMT' => $this->generalService->getEInvoiceArrayValue($taxTotal['TaxSubtotal'][0]['TaxAmount']),
        ];
        $billingReference = $invoice['BillingReference'];
        $refJson = [];
        $firstRef = true;
        if (isset($billingReference)) {
            $data['EINV_BILL_REF'] = $this->generalService->getEInvoiceArrayValue($billingReference[0]['AdditionalDocumentReference'][0]['ID']);
            foreach ($billingReference as $index => $ref) {
                if (isset($ref['InvoiceDocumentReference'])) {
                    $id = $this->generalService->getEInvoiceArrayValue($ref['InvoiceDocumentReference'][0]['ID']);
                    $uuid = $this->generalService->getEInvoiceArrayValue($ref['InvoiceDocumentReference'][0]['UUID']);
                    if ($firstRef) {
                        $firstRef = false;
                        $data['EINV_DOC_REF_ID'] = $id;
                        $data['EINV_UIN_REF'] = $uuid;
                    }
                    $refJson[] = [
                        'id' => $id,
                        'uuid' => $uuid,
                    ];
                }
            }
        }
        $data['EINV_UIN_REF_JSON'] = json_encode($refJson);
        $invoicesLine = $this->getInvoiceLineData($invoice['InvoiceLine'], $this->generalService->getEInvoiceArrayValue($invoice['ID']), );
        ManualIssueEinvoiceHeader::create($data);
        ManualIssueEinvoiceDetail::insert($invoicesLine);
    }
}