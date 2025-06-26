<?php

namespace App\Services\EInvoice;

use App\Enums\DocumentSummaryStatus;
use App\Enums\SubmissionOverallStatus;
use App\Models\EinvoiceDetail;
use App\Models\EinvoiceHeader;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

class EInvoiceTableService
{
    /**
     * Create a new service instance.
     *
     * @return void
     */
    protected string $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Example method for the service.
     */
    public function getHeader(): ?EinvoiceHeader
    {
        return EinvoiceHeader::select(
            'EINV_HDR.ID',
            'EINV_HDR.EINV_ID',
            'EINV_HDR.EINV_SUP_NAME',
            'EINV_HDR.EINV_BUY_NAME',
            'EINV_HDR.EINV_SUP_TIN',
            'EINV_HDR.EINV_SUP_REG_TYPE',
            'EINV_HDR.EINV_SUP_SSM',
            'EINV_HDR.EINV_SUP_ROC',
            'EINV_HDR.EINV_SUP_SST',
            'EINV_HDR.EINV_SUP_TTX',
            'EINV_HDR.EINV_SUP_EMAIL',
            'EINV_HDR.EINV_SUP_MSIC',
            'EINV_HDR.EINV_SUP_BUS_ACT_DESC',
            'EINV_HDR.EINV_SUP_WEBSITE',
            'EINV_HDR.EINV_BUY_ARID',
            'EINV_HDR.EINV_BUY_SHIPMARK',
            'EINV_HDR.EINV_BUY_TIN',
            'EINV_HDR.EINV_BUY_REG_TYPE',
            'EINV_HDR.EINV_BUY_ROC',
            'EINV_HDR.EINV_BUY_SST',
            'EINV_HDR.EINV_BUY_TTX',
            'EINV_HDR.EINV_BUY_EMAIL',
            'EINV_HDR.EINV_SUP_ADDR',
            'EINV_HDR.EINV_SUP_ADDR0',
            'EINV_HDR.EINV_SUP_ADDR1',
            'EINV_HDR.EINV_SUP_ADDR2',
            'EINV_HDR.EINV_SUP_POSTCODE',
            'EINV_HDR.EINV_SUP_CITY',
            'EINV_HDR.EINV_SUP_STATE_ID',
            'EINV_HDR.EINV_SUP_COUNTRY_ID',
            'EINV_HDR.EINV_BUY_ADDR',
            'EINV_HDR.EINV_BUY_ADDR0',
            'EINV_HDR.EINV_BUY_ADDR1',
            'EINV_HDR.EINV_BUY_ADDR2',
            'EINV_HDR.EINV_BUY_POSTCODE',
            'EINV_HDR.EINV_BUY_CITY',
            'EINV_HDR.EINV_BUY_STATE_ID',
            'EINV_HDR.EINV_BUY_COUNTRY_ID',
            'EINV_HDR.EINV_SUP_CONTACT',
            'EINV_HDR.EINV_SUP_FAX',
            'EINV_HDR.EINV_BUY_CONTACT',
            'EINV_HDR.EINV_BUY_FAX',
            'EINV_HDR.EINV_VERSION',
            'EINV_HDR.EINV_TYPE',
            'EINV_HDR.EINV_DOC_REF_ID',
            'EINV_HDR.EINV_UIN_REF',
            'EINV_HDR.EINV_DATE_TIME',
            'EINV_HDR.EINV_DIGITAL_SIGN',
            'EINV_HDR.EINV_CURR',
            'EINV_HDR.EINV_CURR_RATE',
            'EINV_HDR.EINV_BUY_BANK_PAYABLE',
            'EINV_HDR.EINV_REMARK_MODULE',
            'EINV_HDR.EINV_INV_TITLE',
            'EINV_HDR.EINV_FREQ',
            'EINV_HDR.EINV_PERIOD',
            'EINV_HDR.EINV_PAYMENT_MODE',
            'EINV_HDR.EINV_SUP_BANK_ACCT',
            'EINV_HDR.EINV_PAYMENT_TERMS',
            'EINV_HDR.EINV_PREPAYMENT_AMT',
            'EINV_HDR.EINV_PREPAYMENT_DATE',
            'EINV_HDR.EINV_PREPAYMENT_REF',
            'EINV_HDR.EINV_BILL_REF',
            'EINV_HDR.EINV_REF_CUSTOM',
            'EINV_HDR.EINV_SHIP_RCPT_NAME',
            'EINV_HDR.EINV_SHIP_RCPT_ADDR',
            'EINV_HDR.EINV_SHIP_RCPT_ADDR0',
            'EINV_HDR.EINV_SHIP_RCPT_ADDR1',
            'EINV_HDR.EINV_SHIP_RCPT_ADDR2',
            'EINV_HDR.EINV_SHIP_RCPT_POSTCODE',
            'EINV_HDR.EINV_SHIP_RCPT_CITY',
            'EINV_HDR.EINV_SHIP_RCPT_STATE_ID',
            'EINV_HDR.EINV_SHIP_RCPT_COUNTRY_ID',
            'EINV_HDR.EINV_SHIP_RCPT_TIN',
            'EINV_HDR.EINV_SHIP_RCPT_ROC',
            'EINV_HDR.EINV_INCOTERMS',
            'EINV_HDR.EINV_FNL_DEST',
            'EINV_HDR.EINV_D_REMARK',
            'EINV_HDR.EINV_FTA',
            'EINV_HDR.EINV_AUTH_CERT',
            'EINV_HDR.EINV_REF_CUSTOM_2',
            'EINV_HDR.EINV_DETAIL_OTHERS_AMT',
            'EINV_HDR.EINV_DETAIL_OTHERS_REASON',
            'EINV_HDR.EINV_TOTAL_ADD_DISC_AMT',
            'EINV_HDR.EINV_TOTAL_ADD_DISC_REASON',
            'EINV_HDR.EINV_TOTAL_ADD_FEE_AMT',
            'EINV_HDR.EINV_TOTAL_ADD_FEE_REASON',
            'EINV_HDR.EINV_ROUNDING_AMT',
            'EINV_HDR.EINV_TAX_CHAR',
            'EINV_HDR.EINV_HAND_CHAR',
            'EINV_HDR.EINV_TRANS_CHAR',
            'EINV_HDR.EINV_INSR_CHAR',
            'EINV_HDR.EINV_OTH_CHAR',
            'EINV_HDR.EINV_TOTAL_AMT',
            'EINV_HDR.EINV_CREATE_BY_NAME',
            'EINV_HDR.EINV_CREATE_BY',
            'EINV_HDR.EINV_CREATE_DATE',
            'EINV_HDR.EINV_UPD_BY',
            'EINV_HDR.EINV_UPD_DATE',
            'EINV_HDR.EINV_STATUS',
            'EINV_HDR.EINV_VALIDATE_UUID',
            'EINV_HDR.EINV_VALIDATE_STATUS',
            'EINV_HDR.EINV_VALIDATE_DATETIME',
            'EINV_HDR.EINV_VALIDATE_LINK',
            'EINV_HDR.EINV_SUBMISSION_UID',
            'EINV_HDR.EINV_SUBMISSION_DATETIME',
            'EINV_HDR.EINV_RECEIVED_DATETIME',
            'EINV_HDR.EINV_CANCEL_DATETIME',
            'EINV_HDR.EINV_REJECT_REQ_DATETIME',
            'EINV_HDR.EINV_DOC_STATUS_REASON',
            'EINV_MTN_TYPE.EINV_TYPE_NAME'
        )
            ->leftJoin('EINV_MTN_TYPE', 'EINV_MTN_TYPE.EINV_TYPE_CODE', 'EINV_HDR.EINV_TYPE')
            ->where('EINV_ID', $this->id)
            ->first();
    }

    /**
     * Get the details for the invoice.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, EinvoiceDetail>
     */
    public function getDetails(): \Illuminate\Database\Eloquent\Collection
    {
        return EinvoiceDetail::select(
            'ID',
            'EINV_ID',
            'EINV_SEQ',
            'EINV_SOU_NO',
            'EINV_SOU_SEQ',
            'EINV_SEQ_STATUS',
            'EINV_CLASSIFICATION',
            'EINV_PRODUCT_REF1',
            'EINV_PRODUCT_REF2',
            'EINV_PRODUCT_DESC',
            'EINV_UNIT_PRICE',
            'EINV_NETT_UNIT_PRICE',
            'EINV_TAX_TYPE',
            'EINV_TAX_RATE',
            'EINV_TAX_AMT',
            'EINV_TAX_EXEMPTION_DESC',
            'EINV_TAX_AMT_EXEMPTED',
            'EINV_SUBTOTAL',
            'EINV_TOTAL_EXCL_TAX',
            'EINV_TOTAL_INCL_TAX',
            'EINV_TOTAL_NET_AMT',
            'EINV_TOTAL_PAYABLE_AMT',
            'EINV_TOTAL_TAX_AMT_PER_TAX_TYPE',
            'EINV_QTY',
            'EINV_UOM',
            'EINV_UOM_ID',
            'EINV_DISC_RATE',
            'EINV_DISC_AMT',
            'EINV_DISC_REASON',
            'EINV_FEE_RATE',
            'EINV_FEE_AMT',
            'EINV_FEE_REASON',
            'EINV_PROD_TARIFF_CODE',
            'EINV_COUNTRY_OF_ORI',
            'EINV_CREATE_BY',
            'EINV_CREATE_DATE',
            'EINV_UPD_BY',
            'EINV_UPD_DATE'
        )
            ->where('EINV_ID', $this->id)
            ->get();
    }

    public function delete()
    {
        EinvoiceHeader::where('EINV_ID', $this->id)->delete();
        EinvoiceDetail::where('EINV_ID', $this->id)->delete();
    }

    public function markAsInProgress($uid)
    {
        EinvoiceHeader::where('EINV_ID', $this->id)->update([
            'EINV_OVERALL_STATUS' => 'InProgress',
            'EINV_SUBMISSION_UID' => $uid,
        ]);
    }

    public function update($data)
    {
        EinvoiceHeader::where('EINV_ID', $this->id)->update($data);
    }

    public static function getInProgressHeader(int $limit = 100): Collection
    {
        return EinvoiceHeader::select(
            'ID',
            'EINV_ID',
            'EINV_VALIDATE_STATUS',
            'EINV_SUBMISSION_UID',
        )
            ->where('EINV_OVERALL_STATUS', 'InProgress')
            ->limit($limit)
            ->get();
    }

    public function updateHeaderFromEinvoiceApi(
        array $summary,
        string $uid,
        SubmissionOverallStatus $overall_status,
        DocumentSummaryStatus $document_summary_status
    ) {
        $dateTimeIssued = !empty($summary['dateTimeIssued']) ? Carbon::parse($summary['dateTimeIssued'], config('services.einvoice.timezone'))->setTimezone(config('app.timezone')) : null;
        $dateTimeReceived = !empty($summary['dateTimeReceived']) ? Carbon::parse($summary['dateTimeReceived'], config('services.einvoice.timezone'))->setTimeZone(config('app.timezone')) : null;
        $cancelDateTime = !empty($summary['cancelDateTime']) ? Carbon::parse($summary['cancelDateTime'], config('services.einvoice.timezone'))->setTimeZone(config('app.timezone')) : null;
        $rejectRequestDateTime = !empty($summary['rejectRequestDateTime']) ? Carbon::parse($summary['rejectRequestDateTime'], config('services.einvoice.timezone'))->setTimezone(config('app.timezone')) : null;
        EinvoiceHeader::where('EINV_ID', $this->id)->update(
            [
                'EINV_STATUS' => substr($summary['status'], 0, 1),
                'EINV_VALIDATE_UUID' => $summary['uuid'],
                'EINV_VALIDATE_STATUS' => $summary['status'],
                'EINV_OVERALL_STATUS' => $overall_status->value,
                'EINV_VALIDATE_DATETIME' => Carbon::parse($summary['dateTimeValidated'], config('services.einvoice.timezone'))->setTimezone(config('app.timezone')),
                'EINV_VALIDATE_LINK' => $overall_status == SubmissionOverallStatus::INVALID || $document_summary_status == DocumentSummaryStatus::INVALID ? null : config('services.einvoice.qrcode_base_url') . '/' . $summary['uuid'] . '/share/' . $summary['longId'],
                'EINV_SUBMISSION_DATETIME' => $dateTimeIssued,
                'EINV_SUBMISSION_UID' => $uid,
                'EINV_RECEIVED_DATETIME' => $dateTimeReceived,
                'EINV_CANCEL_DATETIME' => $cancelDateTime,
                'EINV_REJECT_REQ_DATETIME' => $rejectRequestDateTime,
                'EINV_DOC_STATUS_REASON' => $summary['documentStatusReason'],
            ]
        );
    }
}
