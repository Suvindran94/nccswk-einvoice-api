<?php

namespace App\Services\Handlers;

use DB;
use Carbon\Carbon;
use App\Contracts\EInvoiceInsertHandlerInterface;
use App\Models\BillHeader;
use App\Models\BillHistory;
use App\Models\BillDetail;
use App\Models\BillDocument;
use Illuminate\Support\Facades\Notification;
use App\Notifications\FinanceManagementApprovalNotification;
use Exception;
use Illuminate\Database\QueryException;
class BillHandler implements EInvoiceInsertHandlerInterface
{
    protected $schema_fm;

    protected $schema_sm;

    protected $schema_admin;

    protected $schema_scm;

    public function __construct(protected string $id, protected int $user_id, protected ?string $approve_status, protected ?string $approve_remark, protected ?int $notification_id)
    {
        $this->schema_fm = config('database.connections.mysql_fm.database');
        $this->schema_sm = config('database.connections.mysql_sm.database');
        $this->schema_admin = config('database.connections.mysql_admin.database');
        $this->schema_scm = config('database.connections.mysql_scm.database');
    }

    public function insertToEInvoiceTables(): void
    {
        try {
            DB::beginTransaction();
            DB::statement("
         insert into {$this->schema_fm}.EINV_HDR ( EINV_ID, EINV_SUP_NAME, EINV_BUY_NAME, EINV_SUP_TIN, EINV_SUP_REG_TYPE, EINV_SUP_SSM, EINV_SUP_ROC, 
                    EINV_SUP_SST, EINV_SUP_TTX, EINV_SUP_EMAIL, EINV_SUP_MSIC, EINV_SUP_BUS_ACT_DESC, EINV_SUP_WEBSITE, 
                    EINV_BUY_ARID, EINV_BUY_SHIPMARK, EINV_BUY_TIN, EINV_BUY_REG_TYPE, EINV_BUY_SSM, EINV_BUY_ROC, EINV_BUY_SST, 
                    EINV_BUY_TTX, EINV_BUY_EMAIL, EINV_BUY_MSIC, EINV_BUY_WEBSITE, EINV_SUP_ADDR, EINV_SUP_ADDR0, EINV_SUP_ADDR1, EINV_SUP_ADDR2, 
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
                select  BILL_HDR.BILL_ID, AP_NAME1, 
                    CO_NAME1, EINV_TIN, (select REG_TYPE from {$this->schema_sm}.MTN_REG_TYPE where ID = EINV_REG_TYPE) as EINV_REG_TYPE, AP_ROC, AP_ROC_NEW, ifnull(AP_SST_REG_NO,'NA') as AP_SST_REG_NO, 
                    'NA' as AP_TTX_REG_NO, ifnull(EINV_AP_EMAIL,'NA') as EINV_AP_EMAIL,
                    '00000' as EINV_SUP_MSIC,
                    'NA' as EINV_SUP_BUS_ACT_DESC,
                    'NA' as EINV_SUP_WEBSITE,
                    MTN_CO.CO_ID as EINV_BUY_ARID, 
                    MTN_CO.CO_ID as EINV_BUY_SHIPMARK, 
                    CO_TIN, CO_REG_TYPE, CO_ROG, CO_ROG_NEW, 
                    ifnull(CO_SST,'NA') as CO_SST, ifnull(CO_TT,'NA') as CO_TTX,  CO_EMAIL as EINV_SUP_EMAIL,
                    CO_MSIC, CO_WEBSITE,
                    concat(trim(ifnull(AP_ADDR1,'')), ' ', trim(ifnull(AP_ADDR2, '')),  trim(concat(' ', trim(ifnull(AP_ADDR3, '')), ' ' , trim(ifnull(AP_ADDR4, '')), ' ')), 
                        ' ', trim(ifnull(AP_MST_SUPPLIER.POSTCODE,'')), ', ', if((select CITY_NAME from {$this->schema_sm}.MTN_CITY where ID = AP_MST_SUPPLIER.CITY_ID) = 'NONE', '', 
                                                                                        concat((select trim(CITY_NAME) from {$this->schema_sm}.MTN_CITY where ID = AP_MST_SUPPLIER.CITY_ID),', ')),
                        if((select MTN_STATE.STATE_NAME  from {$this->schema_sm}.MTN_STATE where ID = AP_MST_SUPPLIER.STATE_ID ) = 'NONE', '',
                            concat((select trim(MTN_STATE.STATE_NAME) from {$this->schema_sm}.MTN_STATE where ID = AP_MST_SUPPLIER.STATE_ID ),', ')),
                        trim((select MTN_COUNTRY.COUNTRY_NAME from {$this->schema_sm}.MTN_COUNTRY where COUNTRY_ISO_CODE = AP_MST_SUPPLIER.COUNTRY_ID) ),
                        '. ') as EINV_SUP_ADDR,
                    AP_ADDR1 as EINV_SUP_ADDR0, AP_ADDR2 as EINV_SUP_ADDR1, concat(trim(ifnull(AP_ADDR3, '')), ' ' , trim(ifnull(AP_ADDR4, ''))) as EINV_SUP_ADDR2, 
                    ifnull(AP_MST_SUPPLIER.POSTCODE,'') as EINV_SUP_POSTCODE, 
                    (select MTN_CITY.CITY_NAME from {$this->schema_sm}.MTN_CITY where ID = AP_MST_SUPPLIER.CITY_ID) as EINV_SUP_CITY, 
                    (select MTN_STATE.EINV_STATE_CODE from {$this->schema_sm}.MTN_STATE where ID = AP_MST_SUPPLIER.STATE_ID ) as EINV_SUP_STATE_ID,
                    (select MTN_COUNTRY.EINV_COUNTRY_CODE from {$this->schema_sm}.MTN_COUNTRY where COUNTRY_ISO_CODE = AP_MST_SUPPLIER.COUNTRY_ID) as EINV_SUP_COUNTRY_ID,
                    concat(trim(ifnull(CO_ADDR1,'')), ' ', trim(ifnull(CO_ADDR2, '')),  trim(concat(' ', trim(ifnull(CO_ADDR3, '')), ' ' , trim(ifnull(CO_ADDR4, '')), ' ')), 
                        ' ', trim(ifnull(MTN_CO.POSTCODE,'')), ' ', trim((select CITY_NAME from {$this->schema_sm}.MTN_CITY where ID = MTN_CO.CITY_ID) ),
                        ', ', trim((select MTN_STATE.STATE_NAME  from {$this->schema_sm}.MTN_STATE where ID = MTN_CO.STATE_ID ) ),
                        ', ', trim((select MTN_COUNTRY.COUNTRY_NAME from {$this->schema_sm}.MTN_COUNTRY where COUNTRY_ISO_CODE = MTN_CO.COUNTRY_ID) ),
                        '. ') as EINV_BUY_ADDR,
                    CO_ADDR1 as EINV_BUY_ADDR0, CO_ADDR2 as EINV_BUY_ADDR1, concat(trim(ifnull(CO_ADDR3, '')), ' ' , trim(ifnull(CO_ADDR4, ''))) as EINV_BUY_ADDR2, 
                    MTN_CO.POSTCODE as EINV_BUY_POSTCODE, 
                    (select CITY_NAME from {$this->schema_sm}.MTN_CITY where ID = MTN_CO.CITY_ID) as EINV_BUY_CITY, 
                    (select MTN_STATE.EINV_STATE_CODE from {$this->schema_sm}.MTN_STATE where ID = MTN_CO.STATE_ID ) as EINV_BUY_STATE_ID,
                    (select MTN_COUNTRY.EINV_COUNTRY_CODE from {$this->schema_sm}.MTN_COUNTRY where COUNTRY_ISO_CODE = MTN_CO.COUNTRY_ID) as EINV_BUY_COUNTRY_ID,
                    AP_TEL1, AP_TEL2, AP_FAX1,  
                    CO_TEL1, CO_TEL2, CO_FAX1,
                    CO_EINV_VERSION as EINV_VERSION,
                    '11' as EINV_TYPE,
                    ifnull(BILL_HDR.BILL_REF,'NA') as EINV_DOC_REF_ID, 
                    'NA' as EINV_UIN_REF, 
                    now() as EINV_DATE_TIME,
                    BILL_HDR.BILL_CURR, 
                    BILL_HDR.LVC_CURR_RATE,
                    null as EINV_BUY_BANK_PAYABLE, 'AP_BILL' as EINV_REMARK_MODULE,
                    'BILLING' as AP_INV_TITLE,
                    null as EINV_FREQ,	
                    null as EINV_PERIOD,	
                    null as EINV_PAYMENT_MODE,	
                    AP_BANK1 as EINV_SUP_BANK_ACCT, 
                    (select TERM_DESC from {$this->schema_sm}.MTN_TERM where MTN_TERM.TERM_ID = BILL_HDR.BILL_TERMS) as EINV_TERM,
                    0 as EINV_PREPAYMENT_AMT, null as EINV_PREPAYMENT_DATE, '-' as EINV_PREPAYMENT_REF, 
                    BILL_HDR.BILL_INV_NO as EINV_BILL_REF, '-' as EINV_REF_CUSTOM, 
                    CO_NAME1 as EINV_SHIP_RCPT_NAME, 
                    concat(trim(ifnull(CO_ADDR1,'')), ' ', trim(ifnull(CO_ADDR2, '')),  trim(concat(' ', trim(ifnull(CO_ADDR3, '')), ' ' , trim(ifnull(CO_ADDR4, '')), ' ')), 
                        ' ', trim(ifnull(MTN_CO.POSTCODE,'')), ' ', trim((select CITY_NAME from {$this->schema_sm}.MTN_CITY where ID = MTN_CO.CITY_ID) ),
                        ', ', trim((select MTN_STATE.STATE_NAME  from {$this->schema_sm}.MTN_STATE where ID = MTN_CO.STATE_ID ) ),
                        ', ', trim((select MTN_COUNTRY.COUNTRY_NAME from {$this->schema_sm}.MTN_COUNTRY where COUNTRY_ISO_CODE = MTN_CO.COUNTRY_ID) ),
                        '. ') as EINV_SHIP_RCPT_ADDR,
                    CO_ADDR1 as EINV_SHIP_RCPT_ADDR0, CO_ADDR2 as EINV_SHIP_RCPT_ADDR1, concat(trim(ifnull(CO_ADDR3, '')), ' ' , trim(ifnull(CO_ADDR4, ''))) as EINV_SHIP_RCPT_ADDR2, 
                    MTN_CO.POSTCODE as EINV_SHIP_RCPT_POSTCODE, 
                    (select CITY_NAME from {$this->schema_sm}.MTN_CITY where ID = MTN_CO.CITY_ID) as EINV_SHIP_RCPT_CITY, 
                    (select MTN_STATE.EINV_STATE_CODE from {$this->schema_sm}.MTN_STATE where ID = MTN_CO.STATE_ID ) as EINV_SHIP_RCPT_STATE_ID,
                    (select MTN_COUNTRY.EINV_COUNTRY_CODE from {$this->schema_sm}.MTN_COUNTRY where COUNTRY_ISO_CODE = MTN_CO.COUNTRY_ID) as EINV_SHIP_RCPT_COUNTRY_ID,
                    CO_TIN as EINV_SHIP_RCPT_TIN, CO_ROG_NEW as EINV_SHIP_RCPT_ROC, 'NA' as EINV_INCOTERMS, 
                    '-' as EINV_FNL_DEST,
                    BILL_REMARK as EINV_D_REMARK,
                    null as EINV_FTA, null as EINV_AUTH_CERT, '-' as EINV_REF_CUSTOM_2, 0 as EINV_DETAIL_OTHERS_AMT, null as EINV_DETAIL_OTHERS_REASON, 
                    0 as EINV_TOTAL_ADD_DISC_AMT, null as EINV_TOTAL_ADD_DISC_REASON, 
                    0 as EINV_TOTAL_ADD_FEE_AMT, 
                    null as EINV_TOTAL_ADD_FEE_REASON, 
                    BILL_HDR.BILL_ROUNDING as EINV_ROUNDING_AMT, BILL_HDR.BILL_TAX_CHAR as EINV_TAX_CHAR, 0  as EINV_HAND_CHAR, 
                    0  as EINV_TRANS_CHAR, 0  as EINV_INSR_CHAR, 0 as EINV_OTH_CHAR, 
                    BILL_HDR.BILL_AMT as EINV_TOTAL_AMT, 
                    (select upper(users.name) from {$this->schema_admin}.users where id = BILL_HDR.BILL_CREATE_BY) as EINV_CREATE_BY_NAME, 
                    BILL_HDR.BILL_CREATE_BY as EINV_CREATE_BY, now() as EINV_CREATE_DATE, BILL_HDR.BILL_UPD_BY as EINV_UPD_BY, BILL_HDR.BILL_UPD_DATE as EINV_UPD_DATE, 
                    'P' as EINV_STATUS       
                from {$this->schema_sm}.MTN_CO, {$this->schema_fm}.BILL_HDR, {$this->schema_scm}.AP_MST_SUPPLIER
                where BILL_HDR.BILL_AP_ID = AP_MST_SUPPLIER.AP_ID
                and BILL_ID = :id;
            ", ['id' => $this->id]);

            DB::statement("insert into {$this->schema_fm}.EINV_DT ( EINV_ID, EINV_SEQ, EINV_SOU_NO, EINV_SOU_SEQ, EINV_SEQ_STATUS, EINV_CLASSIFICATION, EINV_PRODUCT_REF1, EINV_PRODUCT_REF2, 
                    EINV_PRODUCT_DESC, EINV_PRODUCT_REMARKS, EINV_UNIT_PRICE, EINV_NETT_UNIT_PRICE, EINV_TAX_TYPE, EINV_TAX_RATE, EINV_TAX_AMT, 
                    EINV_TAX_EXEMPTION_DESC, EINV_TAX_AMT_EXEMPTED, EINV_SUBTOTAL, EINV_TOTAL_EXCL_TAX, EINV_TOTAL_INCL_TAX, EINV_TOTAL_NET_AMT, 
                    EINV_TOTAL_PAYABLE_AMT, EINV_TOTAL_TAX_AMT_PER_TAX_TYPE, EINV_QTY, EINV_UOM, EINV_UOM_ID, EINV_DISC_RATE, EINV_DISC_AMT, 
                    EINV_DISC_REASON, EINV_FEE_RATE, EINV_FEE_AMT, EINV_FEE_REASON, EINV_PROD_TARIFF_CODE, EINV_COUNTRY_OF_ORI, 
                    EINV_CREATE_BY, EINV_CREATE_DATE, EINV_UPD_BY, EINV_UPD_DATE )

                select BILL_HDR.BILL_ID, BILL_DT.BILL_SEQ, GROUP_CONCAT(distinct BILL_DT.BILL_SOU_NO) as EINV_SOU_NO, BILL_SOU_SEQ as EINV_SOU_SEQ,
                    'A' as EINV_SEQ_STATUS, EINV_CLASSIF_EXPENSE, BILL_DT.BILL_GL_CODE as EINV_PRODUCT_REF1, 
                    null as EINV_PRODUCT_REF2, concat(BILL_DT.BILL_GL_DESC_1, ' ', ifnull(BILL_DT.BILL_GL_DESC_2,'') ) as EINV_PRODUCT_DESC, 
                    BILL_DT.BILL_SEQ_REMARK as EINV_PRODUCT_REMARKS, 
                    BILL_DT.BILL_UNIT_PRICE as EINV_UNIT_PRICE, BILL_DT.BILL_NU_PRICE as EINV_NETT_UNIT_PRICE, 
                    (select MTN_GST_CODE.EINV_TAX_TYPE_CODE from {$this->schema_sm}.MTN_GST_CODE where MTN_GST_CODE.GST_ID = BILL_DT.BILL_GST_ID) as EINV_TAX_TYPE, 
                    (select MTN_GST_CODE.GST_RATE from {$this->schema_sm}.MTN_GST_CODE where MTN_GST_CODE.GST_ID = BILL_DT.BILL_GST_ID) as EINV_TAX_RATE, 
                    BILL_DT.BILL_GST_AMT as EINV_TAX_AMT, null as EINV_TAX_EXEMPTION_DESC, 0 as EINV_TAX_AMT_EXEMPTED, 
                    BILL_DT.BILL_AMT as EINV_SUBTOTAL, BILL_DT.BILL_AMT as EINV_TOTAL_EXCL_TAX, BILL_DT.BILL_AMT as EINV_TOTAL_INCL_TAX, BILL_DT.BILL_AMT as EINV_TOTAL_NET_AMT, 
                    BILL_DT.BILL_AMT as EINV_TOTAL_PAYABLE_AMT, 0 as EINV_TOTAL_TAX_AMT_PER_TAX_TYPE, 
                    BILL_DT.BILL_QTY as EINV_QTY, BILL_DT.BILL_UOM as EINV_UOM, 
                    (select  EINV_CODE from {$this->schema_sm}.MTN_MST where CLASS_ID = 'STK_UOM' and MTN_ID = BILL_DT.BILL_UOM) as EINV_UOM_ID, 
                    0 as EINV_DISC_RATE, BILL_DT.BILL_DISC1 as EINV_DISC_AMT, null as EINV_DISC_REASON, 0 as EINV_FEE_RATE, 0 as EINV_FEE_AMT, null as EINV_FEE_REASON, 
                    null as EINV_PROD_TARIFF_CODE, null as EINV_COUNTRY_OF_ORI, 
                    BILL_DT.BILL_CREATE_BY as EINV_CREATE_BY, BILL_HDR.BILL_CREATE_DATE as EINV_CREATE_DATE, BILL_DT.BILL_UPD_BY as EINV_UPD_BY, BILL_HDR.BILL_UPD_DATE as EINV_UPD_DATE
                from {$this->schema_fm}.BILL_DT, {$this->schema_fm}.BILL_HDR
                where BILL_HDR.BILL_ID = BILL_DT.BILL_ID
                and BILL_DT.BILL_ID =  :id 
                and BILL_DT.deleted_at is null
                group by BILL_HDR.BILL_ID, BILL_DT.BILL_SEQ, BILL_SOU_SEQ,
                    EINV_CLASSIF_EXPENSE, BILL_DT.BILL_GL_CODE, 
                    concat(BILL_DT.BILL_GL_DESC_1, ' ', ifnull(BILL_DT.BILL_GL_DESC_2,'') ), 
                    BILL_DT.BILL_SEQ_REMARK, 
                    BILL_DT.BILL_UNIT_PRICE, BILL_DT.BILL_NU_PRICE, 
                    BILL_DT.BILL_GST_ID, BILL_DT.BILL_GST_AMT, BILL_DT.BILL_AMT, 
                    BILL_DT.BILL_QTY, BILL_DT.BILL_UOM, 
                    BILL_DT.BILL_DISC1, BILL_DT.BILL_CREATE_BY, BILL_HDR.BILL_CREATE_DATE, BILL_DT.BILL_UPD_BY, BILL_HDR.BILL_UPD_DATE;", ['id' => $this->id]);
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
        $billHeader = BillHeader::where('BILL_ID', $this->id)->first();
        $data = [
            'BILL_STATUS' => $this->approve_status,
        ];
        if (!$is_cron_job) {
            $data['BILL_UPD_BY'] = $this->user_id;
            $data['BILL_APV_BY'] = $this->user_id;
            $data['BILL_APV_DATE'] = Carbon::now();
            $data['BILL_APV_STATUS'] = $this->approve_status;
            $data['BILL_APV_REMARK'] = $this->approve_remark;
            $data['BILL_NOTY'] = $this->notification_id;
        }
        $billHeader->update($data);
        $billHeader->refresh();
        if ($this->approve_status == 'A') {
            DB::select('CALL SP_INSERT_GL_BILL(?)', [$billHeader->id]);
        }
        $seq = BillHistory::where('BILL_ID', $billHeader->BILL_ID)->count();
        BillHistory::insert([
            'BILL_ID' => $billHeader->BILL_ID,
            'BILL_APV_SEQ' => $seq + 1,
            'BILL_ACTION' => $this->approve_status,
            'BILL_APV_BY' => $billHeader->BILL_APV_BY,
            'BILL_APV_DATE' => $billHeader->BILL_APV_DATE,
            'BILL_APV_REMARK' => $billHeader->BILL_APV_REMARK,
            'BILL_CREATE_BY' => $billHeader->BILL_UPD_BY,
            'BILL_UPD_BY' => $billHeader->BILL_UPD_BY,
        ]);

        Notification::send($billHeader->creator, new FinanceManagementApprovalNotification($billHeader));

    }

    public function updateToInProgress(): void
    {
        BillHeader::where('BILL_ID', $this->id)->update([
            'BILL_UPD_BY' => $this->user_id,
            'BILL_APV_BY' => $this->user_id,
            'BILL_APV_DATE' => Carbon::now(),
            'BILL_APV_REMARK' => $this->approve_remark,
            'BILL_APV_STATUS' => $this->approve_status,
            'BILL_STATUS' => 'IP',
            'BILL_NOTY' => $this->notification_id,
        ]);
    }

    public function delete(?string $remark, ?int $staff_id, int $delete_user_id, bool $update_approve_information, bool $from_einvoice = false): void
    {
        $billHeader = BillHeader::where('BILL_ID', $this->id)
            ->first();
        if (!$from_einvoice && ($billHeader->BILL_STATUS == "A" || $billHeader->BILL_STATUS == "C")) {
            DB::select('CALL SP_INSERT_GL_BILL_DEL(?,?,?,?)', [$billHeader->BILL_ID, $remark, $staff_id, $delete_user_id]);
        }
        $data = [
            'BILL_STATUS' => 'D',
            'BILL_DELETED_BY' => $delete_user_id,
            'BILL_DELETED_REMARK' => $remark,
            'BILL_UPD_BY' => $delete_user_id,
            'deleted_at' => Carbon::now(),
        ];
        if ($update_approve_information) {
            $data['BILL_APV_BY'] = $this->user_id;
            $data['BILL_APV_DATE'] = Carbon::now();
            $data['BILL_APV_REMARK'] = $this->approve_remark;
            $data['BILL_APV_STATUS'] = $this->approve_status;
            $data['BILL_NOTY'] = $this->notification_id;
        }

        $billHeader->update($data);

        BillDetail::where('BILL_ID', $this->id)
            ->update([
                'BILL_SEQ_STATUS' => 'I',
                'BILL_UPD_BY' => $delete_user_id,
            ]);

        BillDocument::where('BILL_ID', $this->id)->update([
            'STATUS' => 'I',
            'UPD_BY' => $delete_user_id,
        ]);

    }

}
