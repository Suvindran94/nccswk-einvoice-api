<?php

namespace App\Services\Handlers;

use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Notification;
use App\Notifications\FinanceManagementApprovalNotification;
use DB;
use Carbon\Carbon;
use App\Contracts\EInvoiceInsertHandlerInterface;
use App\Models\SdcnHeader;
use App\Models\SdcnDetail;
use App\Models\SdcnHistory;
use App\Models\SdcnDocument;
class SupplierDebitNoteHandler implements EInvoiceInsertHandlerInterface
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
                select  SDCN_HDR.SDCN_ID, AP_NAME1, 
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
                    if(SDCN_HDR.DCN_OPT = 'SCN','12','13') as EINV_TYPE,
                     ifnull((select ifnull(EINV_ID, '-') from {$this->schema_fm}.EINV_HDR 
                    where EINV_ID = (select distinct DCN_SOU_ID from {$this->schema_fm}.SDCN_DT where SDCN_DT.DCN_SEQ_STATUS <> 'D' and  SDCN_DT.deleted_at is null and SDCN_DT.SDCN_ID = SDCN_HDR.SDCN_ID ) 
                    and EINV_HDR.EINV_OVERALL_STATUS = 'Valid' and EINV_HDR.EINV_VALIDATE_STATUS = 'Valid'), '-')  as EINV_DOC_REF_ID, 
                    ifnull((select ifnull(EINV_VALIDATE_UUID, 'NA') from {$this->schema_fm}.EINV_HDR 
                    where EINV_ID = (select distinct DCN_SOU_ID from {$this->schema_fm}.SDCN_DT where SDCN_DT.DCN_SEQ_STATUS <> 'D' and  SDCN_DT.deleted_at is null and SDCN_DT.SDCN_ID = SDCN_HDR.SDCN_ID ) 
                    and EINV_HDR.EINV_OVERALL_STATUS = 'Valid' and EINV_HDR.EINV_VALIDATE_STATUS = 'Valid'), 'NA') as EINV_UIN_REF, 
                    now() as EINV_DATE_TIME,
                    SDCN_HDR.DCN_CURR, 
                    SDCN_HDR.LVC_CURR_RATE,
                    null as EINV_BUY_BANK_PAYABLE, 'AP_SDCN' as EINV_REMARK_MODULE,
                    'SUPPLIER DEBIT/CREDIT NOTES' as AP_INV_TITLE,
                    null as EINV_FREQ,	
                    null as EINV_PERIOD,	
                    null as EINV_PAYMENT_MODE,	
                    AP_BANK1 as EINV_SUP_BANK_ACCT, 
                    (select TERM_DESC from {$this->schema_sm}.MTN_TERM where MTN_TERM.TERM_ID = SDCN_HDR.DCN_TERM) as EINV_TERM,
                    0 as EINV_PREPAYMENT_AMT, null as EINV_PREPAYMENT_DATE, '-' as EINV_PREPAYMENT_REF, 
                    null as EINV_BILL_REF, null as EINV_REF_CUSTOM, 
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
                    DCN_NOTES as EINV_D_REMARK,
                    null as EINV_FTA, null as EINV_AUTH_CERT, '-' as EINV_REF_CUSTOM_2, 0 as EINV_DETAIL_OTHERS_AMT, null as EINV_DETAIL_OTHERS_REASON, 
                    0 as EINV_TOTAL_ADD_DISC_AMT, null as EINV_TOTAL_ADD_DISC_REASON, 0 as EINV_TOTAL_ADD_FEE_AMT, null as EINV_TOTAL_ADD_FEE_REASON, 
                    SDCN_HDR.DCN_ROUNDING as EINV_ROUNDING_AMT, SDCN_HDR.DCN_TAX_CHAR as EINV_TAX_CHAR, 0 as EINV_HAND_CHAR, 
                    0 as EINV_TRANS_CHAR, 0 as EINV_INSR_CHAR, 0 as EINV_OTH_CHAR, 
                    SDCN_HDR.DCN_AMT as EINV_TOTAL_AMT, 
                    (select upper(users.name) from {$this->schema_admin}.users where id = SDCN_HDR.DCN_CREATE_BY) as EINV_CREATE_BY_NAME, 
                    SDCN_HDR.DCN_CREATE_BY as EINV_CREATE_BY, now() as EINV_CREATE_DATE, SDCN_HDR.DCN_UPD_BY as EINV_UPD_BY, SDCN_HDR.DCN_UPD_DATE as EINV_UPD_DATE, 
                    'P' as EINV_STATUS 
                from {$this->schema_sm}.MTN_CO, {$this->schema_fm}.SDCN_HDR, {$this->schema_scm}.AP_MST_SUPPLIER
                where SDCN_HDR.DCN_AP = AP_MST_SUPPLIER.AP_ID
                and SDCN_ID = :id
                ;
            ", ['id' => $this->id]);

            DB::statement("
                Insert into {$this->schema_fm}.EINV_DT ( EINV_ID, EINV_SEQ, EINV_SOU_NO, EINV_SOU_SEQ, EINV_SEQ_STATUS, EINV_CLASSIFICATION, EINV_PRODUCT_REF1, EINV_PRODUCT_REF2, 
                    EINV_PRODUCT_DESC, EINV_PRODUCT_REMARKS, EINV_UNIT_PRICE, EINV_NETT_UNIT_PRICE, EINV_TAX_TYPE, EINV_TAX_RATE, EINV_TAX_AMT, 
                    EINV_TAX_EXEMPTION_DESC, EINV_TAX_AMT_EXEMPTED, EINV_SUBTOTAL, EINV_TOTAL_EXCL_TAX, EINV_TOTAL_INCL_TAX, EINV_TOTAL_NET_AMT, 
                    EINV_TOTAL_PAYABLE_AMT, EINV_TOTAL_TAX_AMT_PER_TAX_TYPE, EINV_QTY, EINV_UOM, EINV_UOM_ID, EINV_DISC_RATE, EINV_DISC_AMT, 
                    EINV_DISC_REASON, EINV_FEE_RATE, EINV_FEE_AMT, EINV_FEE_REASON, EINV_PROD_TARIFF_CODE, EINV_COUNTRY_OF_ORI, 
                    EINV_CREATE_BY, EINV_CREATE_DATE, EINV_UPD_BY, EINV_UPD_DATE )

                select SDCN_HDR.SDCN_ID, SDCN_DT.DCN_SEQ, GROUP_CONCAT(distinct SDCN_DT.DCN_SOU_ID) as EINV_SOU_NO, SDCN_DT.DCN_SOU_SEQ as EINV_SOU_SEQ,
                    'A' as EINV_SEQ_STATUS, EINV_CLASSIF_EXPENSE, SDCN_DT.DCN_STK_CODE as EINV_PRODUCT_REF1, 
                    SDCN_DT.DCN_GL_CODE as EINV_PRODUCT_REF2, concat(SDCN_DT.DCN_GL_DESC_1, ' ', ifnull(SDCN_DT.DCN_GL_DESC_2,'') ) as EINV_PRODUCT_DESC, 
                    SDCN_DT.DCN_SEQ_REMARK as EINV_PRODUCT_REMARKS, 
                    SDCN_DT.DCN_UNIT_PRICE as EINV_UNIT_PRICE, SDCN_DT.DCN_NU_PRICE as EINV_NETT_UNIT_PRICE, 
                    (select MTN_GST_CODE.EINV_TAX_TYPE_CODE from {$this->schema_sm}.MTN_GST_CODE where MTN_GST_CODE.GST_ID = SDCN_HDR.DCN_TAX_ID) as EINV_TAX_TYPE, 
                    (select MTN_GST_CODE.GST_RATE from {$this->schema_sm}.MTN_GST_CODE where MTN_GST_CODE.GST_ID = SDCN_HDR.DCN_TAX_ID) as EINV_TAX_RATE, 
                    SDCN_DT.DCN_GST_AMT as EINV_TAX_AMT, null as EINV_TAX_EXEMPTION_DESC, 0 as EINV_TAX_AMT_EXEMPTED, 
                    SDCN_DT.DCN_AMT as EINV_SUBTOTAL, SDCN_DT.DCN_AMT as EINV_TOTAL_EXCL_TAX, SDCN_DT.DCN_AMT as EINV_TOTAL_INCL_TAX, SDCN_DT.DCN_AMT as EINV_TOTAL_NET_AMT, 
                    SDCN_DT.DCN_AMT as EINV_TOTAL_PAYABLE_AMT, 0 as EINV_TOTAL_TAX_AMT_PER_TAX_TYPE, 
                    SDCN_DT.DCN_QTY as EINV_QTY, SDCN_DT.DCN_UOM as EINV_UOM, 
                    (select  EINV_CODE from {$this->schema_sm}.MTN_MST where CLASS_ID = 'STK_UOM' and MTN_ID = SDCN_DT.DCN_UOM) as EINV_UOM_ID, 
                    0 as EINV_DISC_RATE, 0 as EINV_DISC_AMT, null as EINV_DISC_REASON, 0 as EINV_FEE_RATE, 0 as EINV_FEE_AMT, null as EINV_FEE_REASON, 
                    (select distinct MTN_P_CAT.P_CAT_TARIFF from {$this->schema_sm}.MTN_P_CAT, {$this->schema_sm}.STK_MST 
                    where MTN_P_CAT.P_STK_CAT1 = STK_MST.STK_CAT1 and MTN_P_CAT.P_CAT_STATUS = 'A' 
                    and MTN_P_CAT.deleted_at is null and STK_MST.STK_CODE = SDCN_DT.DCN_STK_CODE ) as EINV_PROD_TARIFF_CODE,
                     null as EINV_COUNTRY_OF_ORI, 
                    SDCN_DT.DCN_CREATE_BY as EINV_CREATE_BY, SDCN_HDR.DCN_CREATE_DATE as EINV_CREATE_DATE, SDCN_DT.DCN_UPD_BY as EINV_UPD_BY, SDCN_HDR.DCN_UPD_DATE as EINV_UPD_DATE
                from {$this->schema_fm}.SDCN_DT, {$this->schema_fm}.SDCN_HDR
                where SDCN_HDR.SDCN_ID = SDCN_DT.SDCN_ID
                and SDCN_DT.SDCN_ID = :id
                and SDCN_DT.deleted_at is null
                group by SDCN_HDR.SDCN_ID, SDCN_DT.DCN_SEQ, DCN_SOU_SEQ,
                    EINV_CLASSIF_EXPENSE, SDCN_DT.DCN_STK_CODE, 
                    SDCN_DT.DCN_GL_CODE, concat(SDCN_DT.DCN_GL_DESC_1, ' ', ifnull(SDCN_DT.DCN_GL_DESC_2,'') ), 
                    SDCN_DT.DCN_SEQ_REMARK, 
                    SDCN_DT.DCN_UNIT_PRICE, SDCN_DT.DCN_NU_PRICE, 
                    SDCN_HDR.DCN_TAX_ID, SDCN_DT.DCN_GST_AMT, SDCN_DT.DCN_AMT, 
                    SDCN_DT.DCN_QTY, SDCN_DT.DCN_UOM, 
                    SDCN_DT.DCN_CREATE_BY, SDCN_HDR.DCN_CREATE_DATE, SDCN_DT.DCN_UPD_BY, SDCN_HDR.DCN_UPD_DATE;
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
        $sdcnHeader = SdcnHeader::where('SDCN_ID', $this->id)->first();
        $data = [
            'DCN_STATUS' => $this->approve_status,
        ];
        if (!$is_cron_job) {
            $data['DCN_UPD_BY'] = $this->user_id;
            $data['DCN_APV_BY'] = $this->user_id;
            $data['DCN_APV_DATE'] = Carbon::now();
            $data['DCN_APV_STATUS'] = $this->approve_status == 'A' ? 'A' : 'N';
            $data['DCN_APV_REMARK'] = $this->approve_remark;
            $data['DCN_NOTY'] = $this->notification_id;
        }
        $sdcnHeader->update($data);
        $sdcnHeader->refresh();

        if ($this->approve_status == 'A') {
            DB::select('CALL SP_INSERT_GL_SDCN(?)', [$sdcnHeader->SDCN_ID]);
        }

        $seq = SdcnHistory::where('SDCN_ID', $sdcnHeader->SDCN_ID)->count();
        SdcnHistory::insert([
            'SDCN_ID' => $sdcnHeader->SDCN_ID,
            'DCN_APV_SEQ' => $seq + 1,
            'DCN_ACTION' => $this->approve_status,
            'DCN_APV_BY' => $sdcnHeader->DCN_APV_BY,
            'DCN_APV_DATE' => $sdcnHeader->DCN_APV_DATE,
            'DCN_APV_REMARK' => $sdcnHeader->DCN_APV_REMARK,
            'DCN_CREATE_BY' => $sdcnHeader->DCN_UPD_BY,
            'DCN_UPD_BY' => $sdcnHeader->DCN_UPD_BY,
        ]);

        Notification::send($sdcnHeader->creator, new FinanceManagementApprovalNotification($sdcnHeader));
    }

    public function updateToInProgress(): void
    {
        SdcnHeader::where('SDCN_ID', $this->id)->update([
            'DCN_UPD_BY' => $this->user_id,
            'DCN_APV_BY' => $this->user_id,
            'DCN_APV_DATE' => Carbon::now(),
            'DCN_APV_REMARK' => $this->approve_remark,
            'DCN_APV_STATUS' => $this->approve_status,
            'DCN_STATUS' => 'IP',
            'DCN_NOTY' => $this->notification_id,
        ]);
    }
    public function delete(?string $remark, ?int $staff_id, int $delete_user_id, bool $update_approve_information, bool $from_einvoice = false): void
    {
        $sdcnHeader = SdcnHeader::where('SDCN_ID', $this->id)->first();
        if ($sdcnHeader->DCN_APV_STATUS == 'A' && !$from_einvoice) {
            DB::select('CALL SP_INSERT_GL_SDCN_DEL(?)', [$sdcnHeader->SDCN_ID]);
        }
        $data = [
            'DCN_STATUS' => 'D',
            'DCN_DELETED_BY' => $delete_user_id,
            'DCN_DELETED_REMARK' => $remark,
            'DCN_UPD_BY' => $delete_user_id,
            'deleted_at' => Carbon::now(),
        ];
        if ($update_approve_information) {
            $data['DCN_APV_BY'] = $this->user_id;
            $data['DCN_APV_DATE'] = Carbon::now();
            $data['DCN_APV_REMARK'] = $this->approve_remark;
            $data['DCN_APV_STATUS'] = $this->approve_status;
            $data['DCN_NOTY'] = $this->notification_id;
        }
        SdcnHeader::where('SDCN_ID', $this->id)->update($data);

        SdcnDetail::where('SDCN_ID', $this->id)->update([
            'DCN_SEQ_STATUS' => 'I',
            'DCN_UPD_BY' => $delete_user_id,
            'deleted_at' => Carbon::now(),
        ]);

        SdcnDocument::where('SDCN_ID', $this->id)->update([
            'STATUS' => 'I',
            'UPD_BY' => $delete_user_id,
            'deleted_at' => Carbon::now(),
        ]);
    }
}
