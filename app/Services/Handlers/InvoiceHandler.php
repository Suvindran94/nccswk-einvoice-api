<?php

namespace App\Services\Handlers;


use DB;
use Carbon\Carbon;
use App\Contracts\EInvoiceInsertHandlerInterface;
use App\Models\InvoiceHeader;
use App\Models\InvoiceDetail;
use App\Models\InvoiceHistory;
use App\Models\InvoiceDocument;
use Illuminate\Support\Facades\Notification;
use App\Notifications\FinanceManagementApprovalNotification;
use Exception;
use Illuminate\Database\QueryException;
class InvoiceHandler implements EInvoiceInsertHandlerInterface
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
                 insert into {$this->schema_fm}.EINV_HDR ( 
                    EINV_ID, EINV_SUP_NAME, EINV_BUY_NAME, EINV_SUP_TIN, EINV_SUP_REG_TYPE, EINV_SUP_SSM, EINV_SUP_ROC, 
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
                    EINV_TOTAL_AMT, EINV_CREATE_BY_NAME, EINV_CREATE_BY, EINV_CREATE_DATE, EINV_UPD_BY, EINV_UPD_DATE, EINV_STATUS 
                )
                select  
                    INV_HDR.INV_ID, CO_NAME1, AR_NAME1, CO_TIN, CO_REG_TYPE, CO_ROG, CO_ROG_NEW, 
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
                    ifnull(INV_HDR.INV_DO_ID,'NA') as EINV_DOC_REF_ID, 
                    'NA' as EINV_UIN_REF, 
                    now() as EINV_DATE_TIME,
                    INV_HDR.INV_CURR, 
                    INV_HDR.LVC_CURR_RATE,
                    AR_BANK, 'AR_SI' as EINV_REMARK_MODULE,
                    AR_INV_TITLE,
                    null as EINV_FREQ,	
                    null as EINV_PERIOD,	
                    null as EINV_PAYMENT_MODE,	
                    null as EINV_SUP_BANK_ACCT, 
                    (select TERM_DESC from {$this->schema_sm}.MTN_TERM where MTN_TERM.TERM_ID = INV_HDR.INV_TERM) as EINV_TERM,
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
                    INV_NOTES as EINV_D_REMARK,
                    null as EINV_FTA, null as EINV_AUTH_CERT, '-' as EINV_REF_CUSTOM_2, 0 as EINV_DETAIL_OTHERS_AMT, null as EINV_DETAIL_OTHERS_REASON, 
                    0 as EINV_TOTAL_ADD_DISC_AMT, null as EINV_TOTAL_ADD_DISC_REASON, 0 as EINV_TOTAL_ADD_FEE_AMT, null as EINV_TOTAL_ADD_FEE_REASON, 
                    INV_HDR.INV_ROUNDING as EINV_ROUNDING_AMT, INV_HDR.INV_TAX_CHAR as EINV_TAX_CHAR, 0 as EINV_HAND_CHAR, 
                    0 as EINV_TRANS_CHAR, 0 as EINV_INSR_CHAR, 0 as EINV_OTH_CHAR, 
                    INV_HDR.INV_AMT as EINV_TOTAL_AMT, 
                    (select upper(users.name) from {$this->schema_admin}.users where id = INV_HDR.INV_CREATE_BY) as EINV_CREATE_BY_NAME, 
                    INV_HDR.INV_CREATE_BY as EINV_CREATE_BY, INV_HDR.INV_APV_DATE as EINV_CREATE_DATE, INV_HDR.INV_UPD_BY as EINV_UPD_BY, INV_HDR.INV_UPD_DATE as EINV_UPD_DATE, 
                    'P' as EINV_STATUS 
                from {$this->schema_sm}.MTN_CO, {$this->schema_fm}.INV_HDR, {$this->schema_sm}.AR_MST_CUSTOMER
                    where INV_HDR.INV_AR = AR_MST_CUSTOMER.AR_ID
                    and INV_ID = :id; 
                ", ['id' => $this->id]);
            DB::statement(
                "  insert into {$this->schema_fm}.EINV_DT ( 
                    EINV_ID, EINV_SEQ, EINV_SOU_NO, EINV_SOU_SEQ, EINV_SEQ_STATUS, EINV_CLASSIFICATION, EINV_PRODUCT_REF1, EINV_PRODUCT_REF2, 
                    EINV_PRODUCT_DESC, EINV_PRODUCT_REMARKS, EINV_UNIT_PRICE, EINV_NETT_UNIT_PRICE, EINV_TAX_TYPE, EINV_TAX_RATE, EINV_TAX_AMT, 
                    EINV_TAX_EXEMPTION_DESC, EINV_TAX_AMT_EXEMPTED, EINV_SUBTOTAL, EINV_TOTAL_EXCL_TAX, EINV_TOTAL_INCL_TAX, EINV_TOTAL_NET_AMT, 
                    EINV_TOTAL_PAYABLE_AMT, EINV_TOTAL_TAX_AMT_PER_TAX_TYPE, EINV_QTY, EINV_UOM, EINV_UOM_ID, EINV_DISC_RATE, EINV_DISC_AMT, 
                    EINV_DISC_REASON, EINV_FEE_RATE, EINV_FEE_AMT, EINV_FEE_REASON, EINV_PROD_TARIFF_CODE, EINV_COUNTRY_OF_ORI, 
                    EINV_CREATE_BY, EINV_CREATE_DATE, EINV_UPD_BY, EINV_UPD_DATE )
                select INV_HDR.INV_ID, INV_DT.INV_SEQ, GROUP_CONCAT(distinct INV_HDR.INV_DO_ID) as EINV_SOU_NO, INV_SOU_SEQ as EINV_SOU_SEQ,
                    'A' as EINV_SEQ_STATUS, EINV_CLASSIF_INCOME, INV_DT.INV_SOU_ID as EINV_PRODUCT_REF1, 
                    INV_DT.INV_GL_CODE as EINV_PRODUCT_REF2, concat(INV_DT.INV_GL_DESC_1, ' ', ifnull(INV_DT.INV_GL_DESC_2,'') ) as EINV_PRODUCT_DESC, 
                    INV_DT.INV_SEQ_REMARK as EINV_PRODUCT_REMARKS, 
                    INV_DT.INV_UNIT_PRICE as EINV_UNIT_PRICE, INV_DT.INV_NU_PRICE as EINV_NETT_UNIT_PRICE, 
                    (select MTN_GST_CODE.EINV_TAX_TYPE_CODE from {$this->schema_sm}.MTN_GST_CODE where MTN_GST_CODE.GST_ID = INV_HDR.INV_TAX_ID) as EINV_TAX_TYPE, 
                    (select MTN_GST_CODE.GST_RATE from {$this->schema_sm}.MTN_GST_CODE where MTN_GST_CODE.GST_ID = INV_HDR.INV_TAX_ID) as EINV_TAX_RATE, 
                    INV_DT.INV_GST_AMT as EINV_TAX_AMT, null as EINV_TAX_EXEMPTION_DESC, 0 as EINV_TAX_AMT_EXEMPTED, 
                    INV_DT.INV_AMT as EINV_SUBTOTAL, INV_DT.INV_AMT as EINV_TOTAL_EXCL_TAX, INV_DT.INV_AMT as EINV_TOTAL_INCL_TAX, INV_DT.INV_AMT as EINV_TOTAL_NET_AMT, 
                    INV_DT.INV_AMT as EINV_TOTAL_PAYABLE_AMT, 0 as EINV_TOTAL_TAX_AMT_PER_TAX_TYPE, 
                    INV_DT.INV_QTY as EINV_QTY, INV_DT.INV_UOM as EINV_UOM, 
                    (select  EINV_CODE from {$this->schema_sm}.MTN_MST where CLASS_ID = 'STK_UOM' and MTN_ID = INV_DT.INV_UOM) as EINV_UOM_ID, 
                    0 as EINV_DISC_RATE, 0 as EINV_DISC_AMT, null as EINV_DISC_REASON, 0 as EINV_FEE_RATE, 0 as EINV_FEE_AMT, null as EINV_FEE_REASON, 
                    (select distinct MTN_P_CAT.P_CAT_TARIFF from {$this->schema_sm}.MTN_P_CAT, {$this->schema_sm}.STK_MST 
                        where MTN_P_CAT.P_STK_CAT1 = STK_MST.STK_CAT1 and MTN_P_CAT.P_CAT_STATUS = 'A' 
                        and MTN_P_CAT.deleted_at is null and STK_MST.STK_CODE = INV_DT.INV_SOU_ID) as EINV_PROD_TARIFF_CODE,
                    null as EINV_COUNTRY_OF_ORI, 
                    INV_DT.INV_CREATE_BY as EINV_CREATE_BY, INV_HDR.INV_CREATE_DATE as EINV_CREATE_DATE, INV_DT.INV_UPD_BY as EINV_UPD_BY, INV_HDR.INV_UPD_DATE as EINV_UPD_DATE
                from {$this->schema_fm}.INV_DT, {$this->schema_fm}.INV_HDR
                    where INV_HDR.INV_ID = INV_DT.INV_ID
                    and INV_DT.INV_ID =  :id
                    and INV_DT.deleted_at IS NULL
                group by INV_HDR.INV_ID, INV_DT.INV_SEQ, INV_SOU_SEQ,
                    EINV_CLASSIF_INCOME, INV_DT.INV_SOU_ID, 
                    INV_DT.INV_GL_CODE, concat(INV_DT.INV_GL_DESC_1, ' ', ifnull(INV_DT.INV_GL_DESC_2,'') ), 
                    INV_DT.INV_SEQ_REMARK, 
                    INV_DT.INV_UNIT_PRICE, INV_DT.INV_UNIT_PRICE, 
                    INV_HDR.INV_TAX_ID, INV_DT.INV_GST_AMT, INV_DT.INV_AMT, 
                    INV_DT.INV_QTY, INV_DT.INV_UOM, INV_DT.INV_CREATE_BY, INV_HDR.INV_CREATE_DATE, INV_DT.INV_UPD_BY, INV_HDR.INV_UPD_DATE,INV_DT.INV_NU_PRICE;"
                ,
                ['id' => $this->id]
            );
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
        $invoiceHeader = InvoiceHeader::where('INV_ID', $this->id)->first();
        $data = [
            'INV_STATUS' => $this->approve_status,
        ];
        if (!$is_cron_job) {
            $data['INV_UPD_BY'] = $this->user_id;
            $data['INV_UPD_DATE'] = Carbon::now();
            $data['INV_APV_BY'] = $this->user_id;
            $data['INV_APV_DATE'] = Carbon::now();
            $data['INV_APV_REMARK'] = $this->approve_remark;
            $data['INV_APV_STATUS'] = $this->approve_status;
            $data['INV_NOTY'] = $this->notification_id;
        }
        $invoiceHeader->update($data);
        $invoiceHeader->refresh();
        if ($this->approve_status == 'A') {
            DB::select('CALL SP_INSERT_GL_INV(?)', [$invoiceHeader->INV_ID]);
        }
        $seq = InvoiceHistory::where('INV_ID', $invoiceHeader->INV_ID)->count();
        InvoiceHistory::insert([
            'INV_ID' => $invoiceHeader->INV_ID,
            'INV_APV_SEQ' => $seq + 1,
            'INV_ACTION' => $this->approve_status,
            'INV_APV_BY' => $invoiceHeader->INV_APV_BY,
            'INV_APV_DATE' => $invoiceHeader->INV_APV_DATE,
            'INV_APV_REMARK' => $invoiceHeader->INV_APV_REMARK,
            'INV_CREATE_BY' => $invoiceHeader->INV_UPD_BY,
            'INV_UPD_BY' => $invoiceHeader->INV_UPD_BY,
            'INV_UPD_DATE' => Carbon::now(),
        ]);

        \Log::info($invoiceHeader);
        \Log::info(json_encode($invoiceHeader->creator));
        Notification::send($invoiceHeader->creator, new FinanceManagementApprovalNotification($invoiceHeader));
    }

    public function updateToInProgress(): void
    {
        InvoiceHeader::where('INV_ID', $this->id)->update([
            'INV_UPD_BY' => $this->user_id,
            'INV_UPD_DATE' => Carbon::now(),
            'INV_APV_BY' => $this->user_id,
            'INV_APV_DATE' => Carbon::now(),
            'INV_APV_REMARK' => $this->approve_remark,
            'INV_APV_STATUS' => $this->approve_status,
            'INV_STATUS' => 'IP',
            'INV_NOTY' => $this->notification_id,
        ]);
    }
    public function delete(?string $remark, ?int $staff_id, int $delete_user_id, bool $update_approve_information): void
    {
        $InvoiceHeader = InvoiceHeader::where('INV_ID', $this->id)->first();

        if ($InvoiceHeader->INV_STATUS == 'A' || $InvoiceHeader->INV_STATUS == 'C') {
            DB::select('CALL SP_INSERT_GL_INV_DEL(?,?,?,?)', [$InvoiceHeader->INV_ID, $remark, $staff_id, $delete_user_id]);
        }

        $data = [
            'INV_UPD_BY' => $delete_user_id,
            'INV_DELETED_BY' => $delete_user_id,
            'INV_UPD_DATE' => Carbon::now(),
            'INV_STATUS' => 'D',
            'INV_DELETED_REMARK' => $remark,
            'deleted_at' => Carbon::now(),
        ];
        if ($update_approve_information) {
            $data['INV_APV_BY'] = $this->user_id;
            $data['INV_APV_DATE'] = Carbon::now();
            $data['INV_APV_REMARK'] = $this->approve_remark;
            $data['INV_APV_STATUS'] = $this->approve_status;
            $data['INV_NOTY'] = $this->notification_id;
        }
        $InvoiceHeader
            ->update($data);

        InvoiceDetail::where('INV_ID', $this->id)
            ->update([
                'INV_SEQ_STATUS' => 'I',
                'INV_UPD_BY' => $delete_user_id,
                'INV_UPD_DATE' => Carbon::now(),
                'deleted_at' => Carbon::now(),
            ]);

        InvoiceDocument::where('INV_ID', $this->id)
            ->update([
                'STATUS' => 'I',
                'UPD_BY' => $delete_user_id,
                'UPD_DATE' => Carbon::now(),
                'deleted_at' => Carbon::now(),
            ]);
    }
}
