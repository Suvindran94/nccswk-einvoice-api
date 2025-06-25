<?php

namespace App\Services\Handlers;

use Illuminate\Support\Facades\Notification;
use App\Notifications\FinanceManagementApprovalNotification;
use DB;
use Carbon\Carbon;
use App\Contracts\EInvoiceInsertHandlerInterface;
use App\Models\ReceiptHeader;
use App\Models\ReceiptDetail;
use App\Models\ReceiptHistory;
use App\Models\ReceiptDetailDeposit;
use App\Models\ReceiptDocument;
use App\Models\ReceiptMatch;
use Exception;
use Illuminate\Database\QueryException;
class ReceiptHandler implements EInvoiceInsertHandlerInterface
{
    protected $schema_fm;

    protected $schema_sm;

    protected $schema_admin;

    public function __construct(protected string $id, protected int $user_id, protected ?string $approve_status, protected ?string $approve_remark, protected ?int $notification_id)
    {
        $this->schema_fm = config('database.connections.mysql_fm.database');
        $this->schema_sm = config('database.connections.mysql_sm.database');
        $this->schema_admin = config('database.connections.mysql_admin.database');
    }

    public function insertToEInvoiceTables(): void
    {
        try {
            DB::beginTransaction();
            DB::statement("
                insert into {$this->schema_fm}.EINV_HDR ( EINV_ID, EINV_SUP_NAME, EINV_BUY_NAME, EINV_SUP_TIN, EINV_SUP_REG_TYPE, EINV_SUP_SSM, EINV_SUP_ROC, 
                    EINV_SUP_SST, EINV_SUP_TTX, EINV_SUP_EMAIL, EINV_SUP_MSIC, EINV_SUP_BUS_ACT_DESC, EINV_SUP_WEBSITE, 
                    EINV_BUY_ARID, EINV_BUY_SHIPMARK, EINV_BUY_TIN, EINV_BUY_REG_TYPE, EINV_BUY_ROC, EINV_BUY_SST, 
                    EINV_BUY_TTX, EINV_BUY_EMAIL, EINV_SUP_ADDR, EINV_SUP_ADDR0, EINV_SUP_ADDR1, EINV_SUP_ADDR2, 
                    EINV_SUP_POSTCODE, EINV_SUP_CITY, EINV_SUP_STATE_ID, EINV_SUP_COUNTRY_ID, EINV_BUY_ADDR, 
                    EINV_BUY_ADDR0, EINV_BUY_ADDR1, EINV_BUY_ADDR2, EINV_BUY_POSTCODE, EINV_BUY_CITY, 
                    EINV_BUY_STATE_ID, EINV_BUY_COUNTRY_ID, EINV_SUP_CONTACT, EINV_SUP_CONTACT_2, EINV_SUP_FAX, EINV_BUY_CONTACT, 
                    EINV_BUY_CONTACT_2, EINV_BUY_FAX, EINV_VERSION, EINV_TYPE, EINV_DOC_REF_ID, EINV_UIN_REF, EINV_DATE_TIME, 
                    EINV_CURR, EINV_CURR_RATE, EINV_BUY_BANK_PAYABLE, EINV_REMARK_MODULE, 
                    EINV_INV_TITLE, EINV_FREQ, EINV_PERIOD, EINV_PAYMENT_MODE, EINV_SUP_BANK_ACCT, EINV_PAYMENT_TERMS, 
                    EINV_PREPAYMENT_AMT, EINV_PREPAYMENT_DATE, EINV_PREPAYMENT_REF, EINV_BILL_REF, EINV_REF_CUSTOM, 
                    EINV_SHIP_RCPT_NAME, EINV_SHIP_RCPT_ADDR, EINV_SHIP_RCPT_ADDR0, EINV_SHIP_RCPT_ADDR1, 
                    EINV_SHIP_RCPT_ADDR2, EINV_SHIP_RCPT_POSTCODE, EINV_SHIP_RCPT_CITY, EINV_SHIP_RCPT_STATE_ID, 
                    EINV_SHIP_RCPT_COUNTRY_ID, EINV_SHIP_RCPT_TIN, EINV_SHIP_RCPT_ROC, EINV_INCOTERMS, EINV_FNL_DEST, 
                    EINV_D_REMARK, EINV_FTA, EINV_AUTH_CERT, EINV_REF_CUSTOM_2, EINV_DETAIL_OTHERS_AMT, EINV_DETAIL_OTHERS_REASON, 
                    EINV_TOTAL_ADD_DISC_AMT, EINV_TOTAL_ADD_DISC_REASON, EINV_TOTAL_ADD_FEE_AMT, EINV_TOTAL_ADD_FEE_REASON, 
                    EINV_ROUNDING_AMT, EINV_TAX_CHAR, EINV_HAND_CHAR, EINV_TRANS_CHAR, EINV_INSR_CHAR, EINV_OTH_CHAR, 
                    EINV_TOTAL_AMT, EINV_CREATE_BY_NAME, EINV_CREATE_BY, EINV_CREATE_DATE, EINV_UPD_BY, EINV_UPD_DATE, EINV_STATUS )
                            
                select  RECEIPT_HDR.RCPT_ID, CO_NAME1, AR_NAME1, CO_TIN, CO_REG_TYPE, CO_ROG, CO_ROG_NEW, 
                    ifnull(CO_SST,'NA') as CO_SST, ifnull(CO_TT,'NA') as CO_TTX,  CO_EMAIL as EINV_SUP_EMAIL, CO_MSIC, CO_BUSINESS_DESC, CO_WEBSITE,
                    AR_ID, AR_NAMES, EINV_TIN, trim((select REG_TYPE from {$this->schema_sm}.MTN_REG_TYPE where ID = AR_MST_CUSTOMER.EINV_REG_TYPE)), AR_ROC_NEW, ifnull(AR_SST_REG_NO,'NA') as AR_SST_REG_NO, 
                    ifnull(AR_TTX_REG_NO,'NA') as AR_TTX_REG_NO, EINV_AR_EMAIL,
                    concat(trim(ifnull(CO_ADDR1,'')), ' ', trim(ifnull(CO_ADDR2, '')),  trim(concat(' ', trim(ifnull(CO_ADDR3, '')), ' ' , trim(ifnull(CO_ADDR4, '')), ' ')), 
                        ' ', trim(ifnull(MTN_CO.POSTCODE,'')), ' ', trim((select CITY_NAME from {$this->schema_sm}.MTN_CITY where ID = MTN_CO.CITY_ID) ),
                        ', ', trim((select MTN_STATE.STATE_NAME  from {$this->schema_sm}.MTN_STATE where ID = MTN_CO.STATE_ID ) ),
                        ', ', trim((select MTN_COUNTRY.COUNTRY_NAME from {$this->schema_sm}.MTN_COUNTRY where COUNTRY_ISO_CODE = MTN_CO.COUNTRY_ID) ),
                        '. '),
                    CO_ADDR1, CO_ADDR2, concat(trim(ifnull(CO_ADDR3, '')), ' ' , trim(ifnull(CO_ADDR4, ''))), 
                    MTN_CO.POSTCODE as CO_POSTCODE, 
                    (select CITY_NAME from {$this->schema_sm}.MTN_CITY where ID = MTN_CO.CITY_ID) as CO_CITY_NAME, 
                    (select MTN_STATE.EINV_STATE_CODE from {$this->schema_sm}.MTN_STATE where ID = MTN_CO.STATE_ID ) as CO_IRB_STATE,
                    (select MTN_COUNTRY.EINV_COUNTRY_CODE from {$this->schema_sm}.MTN_COUNTRY where COUNTRY_ISO_CODE = MTN_CO.COUNTRY_ID) as CO_IRB_COUNTRY,
                    concat(trim(ifnull(AR_ADDR1,'')), ' ', trim(ifnull(AR_ADDR2, '')),  trim(concat(' ', trim(ifnull(AR_ADDR3, '')), ' ' , trim(ifnull(AR_ADDR4, '')), ' ')), 
                        ' ', trim(ifnull(AR_MST_CUSTOMER.POSTCODE,'')), ' ', trim(if((select CITY_NAME from {$this->schema_sm}.MTN_CITY where ID = AR_MST_CUSTOMER.CITY_ID) = 'NONE', '', 
                                                                                        (select CITY_NAME from {$this->schema_sm}.MTN_CITY where ID = AR_MST_CUSTOMER.CITY_ID)) ),
                        ', ', trim((select MTN_STATE.STATE_NAME  from {$this->schema_sm}.MTN_STATE where ID = AR_MST_CUSTOMER.STATE_ID ) ),
                        ', ', trim((select MTN_COUNTRY.COUNTRY_NAME from {$this->schema_sm}.MTN_COUNTRY where COUNTRY_ISO_CODE = AR_MST_CUSTOMER.COUNTRY_ID) ),
                        '. '),
                    AR_ADDR1, AR_ADDR2, concat(trim(ifnull(AR_ADDR3, '')), ' ' , trim(ifnull(AR_ADDR4, ''))), 
                    ifnull(AR_MST_CUSTOMER.POSTCODE,''), 
                    (select MTN_CITY.CITY_NAME from {$this->schema_sm}.MTN_CITY where ID = AR_MST_CUSTOMER.CITY_ID) as CITY_NAME, 
                    (select MTN_STATE.EINV_STATE_CODE from {$this->schema_sm}.MTN_STATE where ID = AR_MST_CUSTOMER.STATE_ID ) as IRB_STATE,
                    (select MTN_COUNTRY.EINV_COUNTRY_CODE from {$this->schema_sm}.MTN_COUNTRY where COUNTRY_ISO_CODE = AR_MST_CUSTOMER.COUNTRY_ID) as IRB_COUNTRY, 
                    CO_TEL1, CO_TEL2, CO_FAX1,
                    AR_TEL1, AR_TEL2, 
                    AR_FAX1, 
                    CO_EINV_VERSION as EINV_VERSION,
                    '01' as EINV_TYPE,
                    RECEIPT_HDR.RCPT_ID as EINV_DOC_REF_ID, 
                    'NA' as EINV_UIN_REF, 
                    /*RECEIPT_HDR.RCPT_APV_DATE*/now() as EINV_DATE_TIME,
                    RECEIPT_HDR.RCPT_CURR, 
                    RECEIPT_HDR.LVC_CURR_RATE,
                    AR_BANK, 'AR_SI' as EINV_REMARK_MODULE,
                    AR_INV_TITLE,
                    null as EINV_FREQ,	
                    null as EINV_PERIOD,	
                    null as EINV_PAYMENT_MODE,	
                    null as EINV_SUP_BANK_ACCT, 
                    (select TERM_DESC from {$this->schema_sm}.MTN_TERM where MTN_TERM.TERM_ID = RECEIPT_HDR.RCPT_TERM) as EINV_TERM,
                    0 as EINV_PREPAYMENT_AMT, null as EINV_PREPAYMENT_DATE, '-' as EINV_PREPAYMENT_REF, 
                    null as EINV_BILL_REF, null as EINV_REF_CUSTOM, 
                    AR_NAME1 as EINV_SHIP_RCPT_NAME, 
                    concat(trim(ifnull(AR_ADDR1,'')), ' ', trim(ifnull(AR_ADDR2, '')),  trim(concat(' ', trim(ifnull(AR_ADDR3, '')), ' ' , trim(ifnull(AR_ADDR4, '')), ' ')), 
                        ' ', trim(ifnull(AR_MST_CUSTOMER.POSTCODE,'')), ' ', trim(if((select CITY_NAME from {$this->schema_sm}.MTN_CITY where ID = AR_MST_CUSTOMER.CITY_ID) = 'NONE', '', 
                                                                                        (select CITY_NAME from {$this->schema_sm}.MTN_CITY where ID = AR_MST_CUSTOMER.CITY_ID)) ),
                        ', ', trim((select MTN_STATE.STATE_NAME  from {$this->schema_sm}.MTN_STATE where ID = AR_MST_CUSTOMER.STATE_ID ) ),
                        ', ', trim((select MTN_COUNTRY.COUNTRY_NAME from {$this->schema_sm}.MTN_COUNTRY where COUNTRY_ISO_CODE = AR_MST_CUSTOMER.COUNTRY_ID) ),
                        '. ') as EINV_SHIP_RCPT_ADDR, 
                    AR_ADDR1 as EINV_SHIP_RCPT_ADDR0, AR_ADDR2 as EINV_SHIP_RCPT_ADDR1, concat(trim(ifnull(AR_ADDR3, '')), ' ' , trim(ifnull(AR_ADDR4, ''))) as EINV_SHIP_RCPT_ADDR2, 
                    ifnull(AR_MST_CUSTOMER.POSTCODE,'') as EINV_SHIP_RCPT_POSTCODE, 
                    (select MTN_CITY.CITY_NAME from {$this->schema_sm}.MTN_CITY where ID = AR_MST_CUSTOMER.CITY_ID) as EINV_SHIP_RCPT_CITY, 
                    (select MTN_STATE.EINV_STATE_CODE from {$this->schema_sm}.MTN_STATE where ID = AR_MST_CUSTOMER.STATE_ID ) as EINV_SHIP_RCPT_STATE_ID,
                    (select MTN_COUNTRY.EINV_COUNTRY_CODE from {$this->schema_sm}.MTN_COUNTRY where COUNTRY_ISO_CODE = AR_MST_CUSTOMER.COUNTRY_ID) as EINV_SHIP_RCPT_COUNTRY_ID, 
                    EINV_TIN as EINV_SHIP_RCPT_TIN, AR_ROC_NEW as EINV_SHIP_RCPT_ROC, 'NA' as EINV_INCOTERMS, 
                    '-' as EINV_FNL_DEST,
                    RCPT_REMARK as EINV_D_REMARK,
                    null as EINV_FTA, null as EINV_AUTH_CERT, '-' as EINV_REF_CUSTOM_2, 0 as EINV_DETAIL_OTHERS_AMT, null as EINV_DETAIL_OTHERS_REASON, 
                    0 as EINV_TOTAL_ADD_DISC_AMT, null as EINV_TOTAL_ADD_DISC_REASON, 0 as EINV_TOTAL_ADD_FEE_AMT, null as EINV_TOTAL_ADD_FEE_REASON, 
                    RECEIPT_HDR.RCPT_ROUNDING as EINV_ROUNDING_AMT, RECEIPT_HDR.RCPT_TAX_CHAR as EINV_TAX_CHAR, 0 as EINV_HAND_CHAR, 
                    0 as EINV_TRANS_CHAR, 0 as EINV_INSR_CHAR, 0 as EINV_OTH_CHAR, 
                    sum(RECEIPT_DT_DEPOSIT.RCPT_AMT_RECV) + RECEIPT_HDR.RCPT_TAX_CHAR as EINV_TOTAL_AMT, 
                    (select upper(users.name) from {$this->schema_admin}.users where id = RECEIPT_HDR.RCPT_CREATE_BY) as EINV_CREATE_BY_NAME, 
                    RECEIPT_HDR.RCPT_CREATE_BY as EINV_CREATE_BY, now() as EINV_CREATE_DATE, RECEIPT_HDR.RCPT_UPD_BY as EINV_UPD_BY, RECEIPT_HDR.RCPT_UPD_DATE as EINV_UPD_DATE, 
                    'P' as EINV_STATUS 
                from {$this->schema_sm}.MTN_CO, {$this->schema_fm}.RECEIPT_HDR, {$this->schema_fm}.RECEIPT_DT_DEPOSIT, {$this->schema_sm}.AR_MST_CUSTOMER
                where RECEIPT_HDR.RCPT_ID = RECEIPT_DT_DEPOSIT.RCPT_ID 
                and RECEIPT_HDR.RCPT_AR_ID = AR_MST_CUSTOMER.AR_ID
                and RECEIPT_HDR.RCPT_ID = :id 
                and RECEIPT_DT_DEPOSIT.RCPT_DEPOSIT = 'Y' 
                and RECEIPT_DT_DEPOSIT.RCPT_SEQ_STATUS not in ('D')
                group by RECEIPT_HDR.RCPT_ID, CO_NAME1, AR_NAME1, CO_TIN, CO_REG_TYPE, CO_ROG, CO_ROG_NEW, 
                    ifnull(CO_SST,'NA'), ifnull(CO_TT,'NA'),  CO_EMAIL, CO_MSIC, CO_BUSINESS_DESC, CO_WEBSITE,
                    AR_ID, AR_NAMES, EINV_TIN, EINV_REG_TYPE, AR_ROC_NEW, ifnull(AR_SST_REG_NO,'NA'), 
                    ifnull(AR_TTX_REG_NO,'NA'), EINV_AR_EMAIL,
                    CO_ADDR1, CO_ADDR2, CO_ADDR3, CO_ADDR4, concat(trim(ifnull(CO_ADDR3, '')), ' ' , trim(ifnull(CO_ADDR4, ''))), 
                    MTN_CO.POSTCODE, 
                    MTN_CO.CITY_ID,
                    MTN_CO.STATE_ID,
                    MTN_CO.COUNTRY_ID,
                    AR_ADDR1, AR_ADDR2, AR_ADDR3, AR_ADDR4, concat(trim(ifnull(AR_ADDR3, '')), ' ' , trim(ifnull(AR_ADDR4, ''))), 
                    AR_MST_CUSTOMER.POSTCODE, 
                    AR_MST_CUSTOMER.CITY_ID, 
                    AR_MST_CUSTOMER.STATE_ID,
                    AR_MST_CUSTOMER.COUNTRY_ID, 
                    CO_TEL1, CO_TEL2, CO_FAX1,
                    AR_TEL1, AR_TEL2, 
                    AR_FAX1, 
                    CO_EINV_VERSION,
                    RECEIPT_HDR.RCPT_ID,
                    RECEIPT_HDR.RCPT_CURR, 
                    RECEIPT_HDR.LVC_CURR_RATE,
                    AR_BANK, 
                    AR_INV_TITLE,
                    RECEIPT_HDR.RCPT_TERM,
                    AR_NAME1, 
                    EINV_TIN, AR_ROC_NEW, 
                    RCPT_REMARK,
                    RECEIPT_HDR.RCPT_ROUNDING, RECEIPT_HDR.RCPT_TAX_CHAR, 
                    RECEIPT_HDR.RCPT_CREATE_BY, RECEIPT_HDR.RCPT_UPD_BY, RECEIPT_HDR.RCPT_UPD_DATE;
            ", ['id' => $this->id]);

            DB::statement("
                insert into {$this->schema_fm}.EINV_DT ( EINV_ID, EINV_SEQ, EINV_SOU_NO, EINV_SOU_SEQ, EINV_SEQ_STATUS, EINV_CLASSIFICATION, EINV_PRODUCT_REF1, EINV_PRODUCT_REF2, 
                    EINV_PRODUCT_DESC, EINV_PRODUCT_REMARKS, EINV_UNIT_PRICE, EINV_NETT_UNIT_PRICE, EINV_TAX_TYPE, EINV_TAX_RATE, EINV_TAX_AMT, 
                    EINV_TAX_EXEMPTION_DESC, EINV_TAX_AMT_EXEMPTED, EINV_SUBTOTAL, EINV_TOTAL_EXCL_TAX, EINV_TOTAL_INCL_TAX, EINV_TOTAL_NET_AMT, 
                    EINV_TOTAL_PAYABLE_AMT, EINV_TOTAL_TAX_AMT_PER_TAX_TYPE, EINV_QTY, EINV_UOM, EINV_UOM_ID, EINV_DISC_RATE, EINV_DISC_AMT, 
                    EINV_DISC_REASON, EINV_FEE_RATE, EINV_FEE_AMT, EINV_FEE_REASON, EINV_PROD_TARIFF_CODE, EINV_COUNTRY_OF_ORI, 
                    EINV_CREATE_BY, EINV_CREATE_DATE, EINV_UPD_BY, EINV_UPD_DATE )

                select RECEIPT_HDR.RCPT_ID, RECEIPT_DT.RCPT_SEQ, GROUP_CONCAT(distinct RECEIPT_DT_DEPOSIT.RCPT_QUO_ID) as EINV_SOU_NO, RECEIPT_DT_DEPOSIT.RCPT_SEQ as EINV_SOU_SEQ,
                    'A' as EINV_SEQ_STATUS, '022' as EINV_CLASSIF_INCOME, RECEIPT_HDR.RCPT_SOURCE_ID as EINV_PRODUCT_REF1, 
                    null as EINV_PRODUCT_REF2, 'DEPOSIT' as EINV_PRODUCT_DESC, 
                    RECEIPT_DT.RCPT_SEQ_REMARK as EINV_PRODUCT_REMARKS, 
                    sum(RECEIPT_DT_DEPOSIT.RCPT_AMT_RECV) as EINV_UNIT_PRICE, sum(RECEIPT_DT_DEPOSIT.RCPT_AMT_RECV) as EINV_NETT_UNIT_PRICE, 
                    (select MTN_GST_CODE.EINV_TAX_TYPE_CODE from {$this->schema_sm}.MTN_GST_CODE where MTN_GST_CODE.GST_ID = RECEIPT_HDR.RCPT_TAX_ID) as EINV_TAX_TYPE, 
                    (select MTN_GST_CODE.GST_RATE from {$this->schema_sm}.MTN_GST_CODE where MTN_GST_CODE.GST_ID = RECEIPT_HDR.RCPT_TAX_ID) as EINV_TAX_RATE, 
                    RECEIPT_DT.RCPT_GST_AMT as EINV_TAX_AMT, null as EINV_TAX_EXEMPTION_DESC, 0 as EINV_TAX_AMT_EXEMPTED, 
                    sum(RECEIPT_DT_DEPOSIT.RCPT_AMT_RECV) as EINV_SUBTOTAL, sum(RECEIPT_DT_DEPOSIT.RCPT_AMT_RECV) as EINV_TOTAL_EXCL_TAX, sum(RECEIPT_DT_DEPOSIT.RCPT_AMT_RECV) + RECEIPT_DT.RCPT_GST_AMT as EINV_TOTAL_INCL_TAX, 
                    sum(RECEIPT_DT_DEPOSIT.RCPT_AMT_RECV) as EINV_TOTAL_NET_AMT, 
                    sum(RECEIPT_DT_DEPOSIT.RCPT_AMT_RECV) + RECEIPT_DT.RCPT_GST_AMT as EINV_TOTAL_PAYABLE_AMT, 0 as EINV_TOTAL_TAX_AMT_PER_TAX_TYPE, 
                    0 as EINV_QTY, null as EINV_UOM, 
                    'XNA' as EINV_UOM_ID, 
                    0 as EINV_DISC_RATE, 0 as EINV_DISC_AMT, null as EINV_DISC_REASON, 0 as EINV_FEE_RATE, 0 as EINV_FEE_AMT, null as EINV_FEE_REASON, 
                    null as EINV_PROD_TARIFF_CODE, null as EINV_COUNTRY_OF_ORI, 
                    RECEIPT_DT.RCPT_CREATE_BY as EINV_CREATE_BY, RECEIPT_HDR.RCPT_CREATE_DATE as EINV_CREATE_DATE, RECEIPT_DT.RCPT_UPD_BY as EINV_UPD_BY, RECEIPT_HDR.RCPT_UPD_DATE as EINV_UPD_DATE
                from {$this->schema_fm}.RECEIPT_DT, {$this->schema_fm}.RECEIPT_HDR, {$this->schema_fm}.RECEIPT_DT_DEPOSIT
                    where RECEIPT_HDR.RCPT_ID = RECEIPT_DT.RCPT_ID
                    and RECEIPT_DT_DEPOSIT.RCPT_ID = RECEIPT_HDR.RCPT_ID
                    and RECEIPT_DT_DEPOSIT.RCPT_DT_SEQ = RECEIPT_DT.RCPT_SEQ
                    and RECEIPT_DT_DEPOSIT.RCPT_DEPOSIT = 'Y' 
                    and RECEIPT_DT_DEPOSIT.RCPT_SEQ_STATUS not in ('D')
                    and RECEIPT_DT.RCPT_ID = :id 
                    and RECEIPT_DT.deleted_at IS NULL
                    and RECEIPT_DT_DEPOSIT.deleted_at IS NULL
                group by RECEIPT_HDR.RCPT_ID, RECEIPT_DT.RCPT_SEQ, RECEIPT_DT_DEPOSIT.RCPT_SEQ,
                RECEIPT_HDR.RCPT_SOURCE_ID,
                RECEIPT_DT.RCPT_SEQ_REMARK, 
                RECEIPT_HDR.RCPT_TAX_ID, RECEIPT_DT.RCPT_GST_AMT, 
                RECEIPT_DT.RCPT_CREATE_BY, RECEIPT_HDR.RCPT_CREATE_DATE, RECEIPT_DT.RCPT_UPD_BY, RECEIPT_HDR.RCPT_UPD_DATE;
            ", ['id' => $this->id]);
            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();
            $error = $e->errorInfo[2] ?? 'An unexpected database error occurred.';
            throw new Exception($error, 400, $e);
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function update(bool $is_cron_job): void
    {
        $receiptHeader = ReceiptHeader::where('RCPT_ID', $this->id)->first();
        $data = [
            'RCPT_STATUS' => $this->approve_status,
        ];
        if (!$is_cron_job) {
            $data['RCPT_UPD_BY'] = $this->user_id;
            $data['RCPT_APV_BY'] = $this->user_id;
            $data['RCPT_APV_DATE'] = Carbon::now();
            $data['RCPT_APV_STATUS'] = $this->approve_status;
            $data['RCPT_APV_REMARK'] = $this->approve_remark;
            $data['RCPT_NOTY'] = $this->notification_id;
        }
        $receiptHeader->update($data);
        $receiptHeader->refresh();

        if ($this->approve_status == 'A') {
            DB::select('CALL SP_INSERT_GL_REVENUE(?)', [$receiptHeader->RCPT_ID]);
        }
        $seq = ReceiptHistory::where('RCPT_ID', $receiptHeader->RCPT_ID)->count();
        ReceiptHistory::insert([
            'RCPT_ID' => $receiptHeader->RCPT_ID,
            'RCPT_APV_SEQ' => $seq + 1,
            'RCPT_ACTION' => $this->approve_status,
            'RCPT_APV_BY' => $receiptHeader->RCPT_APV_BY,
            'RCPT_APV_DATE' => $receiptHeader->RCPT_APV_DATE,
            'RCPT_APV_REMARK' => $receiptHeader->RCPT_APV_REMARK,
            'RCPT_CREATE_BY' => $receiptHeader->RCPT_UPD_BY,
            'RCPT_UPD_BY' => $receiptHeader->RCPT_UPD_BY,
        ]);

        Notification::send($receiptHeader->creator, new FinanceManagementApprovalNotification($receiptHeader));
    }

    public function updateToInProgress(): void
    {
        ReceiptHeader::where('RCPT_ID', $this->id)->update([
            'RCPT_UPD_BY' => $this->user_id,
            'RCPT_APV_BY' => $this->user_id,
            'RCPT_APV_DATE' => Carbon::now(),
            'RCPT_APV_REMARK' => $this->approve_remark,
            'RCPT_APV_STATUS' => $this->approve_status,
            'RCPT_STATUS' => 'IP',
            'RCPT_NOTY' => $this->notification_id,
        ]);
    }
    public function delete(?string $remark, ?int $staff_id, int $delete_user_id, bool $update_approve_information): void
    {
        $receiptHeader = ReceiptHeader::where('RCPT_ID', $this->id)->first();
        if ($receiptHeader->RCPT_APV_STATUS == 'A') {
            DB::select('CALL SP_INSERT_GL_REVENUE_DEL(?)', [$receiptHeader->RCPT_ID]);
        }
        $data = [
            'RCPT_DELETED_BY' => $delete_user_id,
            'RCPT_UPD_BY' => $delete_user_id,
            'RCPT_STATUS' => 'D',
            'RCPT_DELETED_REMARK' => $remark,
            'deleted_at' => Carbon::now(),
        ];
        if ($update_approve_information) {
            $data['RCPT_APV_BY'] = $this->user_id;
            $data['RCPT_APV_DATE'] = Carbon::now();
            $data['RCPT_APV_REMARK'] = $this->approve_remark;
            $data['RCPT_APV_STATUS'] = $this->approve_status;
            $data['RCPT_NOTY'] = $this->notification_id;
        }
        $receiptHeader
            ->update($data);

        ReceiptDetail::where('RCPT_ID', $this->id)
            ->update([
                'RCPT_SEQ_STATUS' => 'I',
                'RCPT_UPD_BY' => $delete_user_id,
                'deleted_at' => Carbon::now(),
            ]);

        ReceiptDetailDeposit::where('RCPT_ID', $this->id)
            ->update([
                'RCPT_SEQ_STATUS' => 'I',
                'RCPT_UPD_BY' => $delete_user_id,
                'deleted_at' => Carbon::now(),
            ]);

        ReceiptDocument::where('RCPT_ID', $this->id)
            ->update([
                'STATUS' => 'I',
                'UPD_BY' => $delete_user_id,
                'deleted_at' => Carbon::now(),
            ]);

        ReceiptMatch::where('RCPT_ID', $this->id)
            ->update([
                'RCPT_SEQ_STATUS' => 'I',
                'RCPT_UPD_BY' => $delete_user_id,
                'deleted_at' => Carbon::now(),
            ]);
    }
}

