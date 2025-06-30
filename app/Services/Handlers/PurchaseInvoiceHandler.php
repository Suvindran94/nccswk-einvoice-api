<?php

namespace App\Services\Handlers;

use App\Mail\PurhcaseInvoiceApprovalMail;
use App\Models\GoodReceiveNoteDetail;
use App\Models\GoodReceiveNoteHeader;
use App\Notifications\SupplyChainManagementApprovalNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use DB;
use Carbon\Carbon;
use App\Contracts\EInvoiceInsertHandlerInterface;
use App\Models\PurchaseInvoiceHeader;
use App\Models\PurchaseInvoiceDetail;
use App\Models\PurchaseInvoiceHistory;
use Exception;
use Illuminate\Database\QueryException;

class PurchaseInvoiceHandler implements EInvoiceInsertHandlerInterface
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
                select  PI_HDR.PI_ID, AP_NAME1, 
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
                        ifnull(PI_HDR.PI_REF,'NA') as EINV_DOC_REF_ID, 
                        'NA' as EINV_UIN_REF, 
                        now() as EINV_DATE_TIME,
                        PI_HDR.PI_CURR, 
                        PI_HDR.LVC_CURR_RATE,
                        null as EINV_BUY_BANK_PAYABLE, 'AP_PI' as EINV_REMARK_MODULE,
                        'PURCHASE INVOICE' as AP_INV_TITLE,
                        null as EINV_FREQ,	
                        null as EINV_PERIOD,	
                        null as EINV_PAYMENT_MODE,	
                        AP_BANK1 as EINV_SUP_BANK_ACCT, 
                        (select TERM_DESC from {$this->schema_sm}.MTN_TERM where MTN_TERM.TERM_ID = PI_HDR.PI_TERM) as EINV_TERM,
                        0 as EINV_PREPAYMENT_AMT, null as EINV_PREPAYMENT_DATE, '-' as EINV_PREPAYMENT_REF, 
                        PI_HDR.PI_INV_NO as EINV_BILL_REF, PI_HDR.PI_K1_ID as EINV_REF_CUSTOM, 
                        TBL_DELIVERY.AP_D_NAME as EINV_SHIP_RCPT_NAME, 
                        concat(trim(ifnull(TBL_DELIVERY.AP_D_ADDR1,'')), ' ', trim(ifnull(TBL_DELIVERY.AP_D_ADDR2, '')),  trim(concat(' ', trim(ifnull(TBL_DELIVERY.AP_D_ADDR3, '')), ' ' , trim(ifnull(TBL_DELIVERY.AP_D_ADDR4, '')), ' ')), 
                            ' ', trim(ifnull(TBL_DELIVERY.AP_D_POSTCODE,'')), ' ', trim((select CITY_NAME from {$this->schema_sm}.MTN_CITY where ID = TBL_DELIVERY.AP_D_CITY_ID) ),
                            ', ', trim((select MTN_STATE.STATE_NAME  from {$this->schema_sm}.MTN_STATE where ID = TBL_DELIVERY.AP_D_STATE_ID ) ),
                            ', ', trim((select MTN_COUNTRY.COUNTRY_NAME from {$this->schema_sm}.MTN_COUNTRY where COUNTRY_ISO_CODE = TBL_DELIVERY.AP_D_COUNTRY_ID) ),
                            '. ') as EINV_SHIP_RCPT_ADDR,
                        TBL_DELIVERY.AP_D_ADDR1 as EINV_SHIP_RCPT_ADDR0, TBL_DELIVERY.AP_D_ADDR2 as EINV_SHIP_RCPT_ADDR1, concat(trim(ifnull(TBL_DELIVERY.AP_D_ADDR3, '')), ' ' , trim(ifnull(TBL_DELIVERY.AP_D_ADDR4, ''))) as EINV_SHIP_RCPT_ADDR2, 
                        TBL_DELIVERY.AP_D_POSTCODE as EINV_SHIP_RCPT_POSTCODE, 
                        (select CITY_NAME from {$this->schema_sm}.MTN_CITY where ID = TBL_DELIVERY.AP_D_CITY_ID) as EINV_SHIP_RCPT_CITY, 
                        (select MTN_STATE.EINV_STATE_CODE from {$this->schema_sm}.MTN_STATE where ID = TBL_DELIVERY.AP_D_STATE_ID ) as EINV_SHIP_RCPT_STATE_ID,
                        (select MTN_COUNTRY.EINV_COUNTRY_CODE from {$this->schema_sm}.MTN_COUNTRY where COUNTRY_ISO_CODE = TBL_DELIVERY.AP_D_COUNTRY_ID) as EINV_SHIP_RCPT_COUNTRY_ID,
                        CO_TIN as EINV_SHIP_RCPT_TIN, CO_ROG_NEW as EINV_SHIP_RCPT_ROC, 
                        'NA' as EINV_INCOTERMS, 
                        '-' as EINV_FNL_DEST,
                        PI_D_REMARK as EINV_D_REMARK,
                        null as EINV_FTA, null as EINV_AUTH_CERT, '-' as EINV_REF_CUSTOM_2, 0 as EINV_DETAIL_OTHERS_AMT, null as EINV_DETAIL_OTHERS_REASON, 
                        0 as EINV_TOTAL_ADD_DISC_AMT, null as EINV_TOTAL_ADD_DISC_REASON, 
                        (PI_HAND_CHAR + PI_TRANS_CHAR + PI_INSR_CHAR + PI_OTH_CHAR) as EINV_TOTAL_ADD_FEE_AMT, 
                        concat_ws(', ', IF(PI_HAND_CHAR > 0 , 'HANDLING', null),  IF(PI_TRANS_CHAR > 0 , 'FREIGHT', null), IF(PI_INSR_CHAR > 0 , 'INSURANCE', null)  , IF(PI_OTH_CHAR > 0 , 'OTHER CHARGES', null) ) as EINV_TOTAL_ADD_FEE_REASON, 
                        PI_HDR.PI_ROUNDING as EINV_ROUNDING_AMT, PI_HDR.PI_TAX_CHAR as EINV_TAX_CHAR, PI_HDR.PI_HAND_CHAR  as EINV_HAND_CHAR, 
                        PI_HDR.PI_TRANS_CHAR  as EINV_TRANS_CHAR, PI_HDR.PI_INSR_CHAR  as EINV_INSR_CHAR, PI_HDR.PI_OTH_CHAR  as EINV_OTH_CHAR, 
                        PI_HDR.PI_AMT as EINV_TOTAL_AMT, 
                        (select upper(users.name) from {$this->schema_admin}.users where id = PI_HDR.PI_CREATE_BY) as EINV_CREATE_BY_NAME, 
                        PI_HDR.PI_CREATE_BY as EINV_CREATE_BY, now() as EINV_CREATE_DATE, PI_HDR.PI_UPD_BY as EINV_UPD_BY, PI_HDR.PI_UPD_DATE as EINV_UPD_DATE, 
                        'P' as EINV_STATUS       
                from {$this->schema_sm}.MTN_CO, {$this->schema_scm}.AP_MST_SUPPLIER, {$this->schema_scm}.PI_HDR left join 
                    (select distinct PI_DT.PI_ID, AP_MST_DELIVERY.AP_D_NAME, AP_MST_DELIVERY.AP_D_ADDR1, AP_MST_DELIVERY.AP_D_ADDR2, AP_MST_DELIVERY.AP_D_ADDR3, 
                                    AP_MST_DELIVERY.AP_D_ADDR4, AP_MST_DELIVERY.AP_D_POSTCODE, AP_MST_DELIVERY.AP_D_CITY_ID, AP_MST_DELIVERY.AP_D_STATE_ID, 
                                    AP_MST_DELIVERY.AP_D_COUNTRY_ID   
                    from {$this->schema_scm}.AP_MST_DELIVERY, {$this->schema_scm}.PI_DT 
                    where PI_DT.PI_DELV_LOC = AP_MST_DELIVERY.AP_D_CODE
                    and PI_DT.deleted_at is null
                    and AP_MST_DELIVERY.deleted_at is null
                    and AP_MST_DELIVERY.AP_D_STATUS = 'A'
                    and PI_DT.PI_ID = :id1
                    limit 1
                    ) TBL_DELIVERY 
                    on PI_HDR.PI_ID = TBL_DELIVERY.PI_ID 
                where PI_HDR.PI_AP = AP_MST_SUPPLIER.AP_ID
                and PI_HDR.PI_ID = :id2;
            ", ['id1' => $this->id, 'id2' => $this->id]);
            DB::statement("
                    insert into {$this->schema_fm}.EINV_DT ( EINV_ID, EINV_SEQ, EINV_SOU_NO, EINV_SOU_SEQ, EINV_SEQ_STATUS, EINV_CLASSIFICATION, EINV_PRODUCT_REF1, EINV_PRODUCT_REF2, 
                    EINV_PRODUCT_DESC, EINV_PRODUCT_REMARKS, EINV_UNIT_PRICE, EINV_NETT_UNIT_PRICE, EINV_TAX_TYPE, EINV_TAX_RATE, EINV_TAX_AMT, 
                    EINV_TAX_EXEMPTION_DESC, EINV_TAX_AMT_EXEMPTED, EINV_SUBTOTAL, EINV_TOTAL_EXCL_TAX, EINV_TOTAL_INCL_TAX, EINV_TOTAL_NET_AMT, 
                    EINV_TOTAL_PAYABLE_AMT, EINV_TOTAL_TAX_AMT_PER_TAX_TYPE, EINV_QTY, EINV_UOM, EINV_UOM_ID, EINV_DISC_RATE, EINV_DISC_AMT, 
                    EINV_DISC_REASON, EINV_FEE_RATE, EINV_FEE_AMT, EINV_FEE_REASON, EINV_PROD_TARIFF_CODE, EINV_COUNTRY_OF_ORI, 
                    EINV_CREATE_BY, EINV_CREATE_DATE, EINV_UPD_BY, EINV_UPD_DATE )
                select PI_HDR.PI_ID, PI_DT.PI_SEQ, GROUP_CONCAT(distinct PI_DT.PI_SOU_NO) as EINV_SOU_NO, PI_SOU_SEQ as EINV_SOU_SEQ,
                    'A' as EINV_SEQ_STATUS, EINV_CLASSIF_EXPENSE, PI_DT.PI_GL_CODE as EINV_PRODUCT_REF1, 
                    PI_DT.PI_STK_CODE as EINV_PRODUCT_REF2, concat(PI_DT.PI_STK_DESC_1, ' ', ifnull(PI_DT.PI_STK_DESC_2,'') ) as EINV_PRODUCT_DESC, 
                    PI_DT.PI_SEQ_REMARK as EINV_PRODUCT_REMARKS, 
                    PI_DT.PI_UNIT_PRICE as EINV_UNIT_PRICE, PI_DT.PI_NU_PRICE as EINV_NETT_UNIT_PRICE, 
                    (select MTN_GST_CODE.EINV_TAX_TYPE_CODE from {$this->schema_sm}.MTN_GST_CODE where MTN_GST_CODE.GST_ID = PI_DT.PI_GST_ID) as EINV_TAX_TYPE, 
                    (select MTN_GST_CODE.GST_RATE from {$this->schema_sm}.MTN_GST_CODE where MTN_GST_CODE.GST_ID = PI_DT.PI_GST_ID) as EINV_TAX_RATE, 
                    PI_DT.PI_GST_AMT as EINV_TAX_AMT, null as EINV_TAX_EXEMPTION_DESC, 0 as EINV_TAX_AMT_EXEMPTED, 
                    PI_DT.PI_AMT as EINV_SUBTOTAL, PI_DT.PI_AMT as EINV_TOTAL_EXCL_TAX, PI_DT.PI_AMT as EINV_TOTAL_INCL_TAX, PI_DT.PI_AMT as EINV_TOTAL_NET_AMT, 
                    PI_DT.PI_AMT as EINV_TOTAL_PAYABLE_AMT, 0 as EINV_TOTAL_TAX_AMT_PER_TAX_TYPE, 
                    PI_DT.PI_QTY as EINV_QTY, PI_DT.PI_UOM as EINV_UOM, 
                    (select  EINV_CODE from {$this->schema_sm}.MTN_MST where CLASS_ID = 'STK_UOM' and MTN_ID = PI_DT.PI_UOM) as EINV_UOM_ID, 
                    0 as EINV_DISC_RATE, PI_DT.PI_DISC1 as EINV_DISC_AMT, null as EINV_DISC_REASON, 0 as EINV_FEE_RATE, 0 as EINV_FEE_AMT, null as EINV_FEE_REASON, 
                    (select distinct MTN_P_CAT.P_CAT_TARIFF from {$this->schema_sm}.MTN_P_CAT, {$this->schema_sm}.STK_MST 
                        where MTN_P_CAT.P_STK_CAT1 = STK_MST.STK_CAT1 and MTN_P_CAT.P_CAT_STATUS = 'A' 
                        and MTN_P_CAT.deleted_at is null and STK_MST.STK_CODE = PI_DT.PI_STK_CODE ) as EINV_PROD_TARIFF_CODE,
                     null as EINV_COUNTRY_OF_ORI, 
                    PI_DT.PI_CREATE_BY as EINV_CREATE_BY, PI_HDR.PI_CREATE_DATE as EINV_CREATE_DATE, PI_DT.PI_UPD_BY as EINV_UPD_BY, PI_HDR.PI_UPD_DATE as EINV_UPD_DATE
                from {$this->schema_scm}.PI_DT, {$this->schema_scm}.PI_HDR
                where PI_HDR.PI_ID = PI_DT.PI_ID
                and PI_DT.PI_ID = :id
                and PI_DT.deleted_at is null
                group by PI_HDR.PI_ID, PI_DT.PI_SEQ, PI_SOU_SEQ,
                    EINV_CLASSIF_EXPENSE, PI_DT.PI_GL_CODE, 
                    PI_DT.PI_STK_CODE, concat(PI_DT.PI_STK_DESC_1, ' ', ifnull(PI_DT.PI_STK_DESC_2,'') ), 
                    PI_DT.PI_SEQ_REMARK, 
                    PI_DT.PI_UNIT_PRICE, PI_DT.PI_NU_PRICE, 
                    PI_DT.PI_GST_ID, PI_DT.PI_GST_AMT, PI_DT.PI_AMT, 
                    PI_DT.PI_QTY, PI_DT.PI_UOM, 
                    PI_DT.PI_DISC1, PI_DT.PI_CREATE_BY, PI_HDR.PI_CREATE_DATE, PI_DT.PI_UPD_BY, PI_HDR.PI_UPD_DATE;
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
        $purchaseInvoiceHeader = PurchaseInvoiceHeader::where('PI_ID', $this->id)->first();
        $data = [
            'PI_STATUS' => $this->approve_status == "A" ? "A" : "N"
        ];
        if (!$is_cron_job) {
            $data['PI_UPD_BY'] = $this->user_id;
            $data['PI_APV_BY'] = $this->user_id;
            $data['PI_APV_DATE'] = Carbon::now();
            $data['PI_APV_REMARK'] = $this->approve_remark;
            $data['PI_APV_STATUS'] = $this->approve_status;
            $data['PI_NOTY'] = $this->notification_id;
        }
        $purchaseInvoiceHeader->update($data);
        $purchaseInvoiceHeader->refresh();
        if ($this->approve_status == 'A') {
            DB::connection('mysql')->select('CALL SP_INSERT_GL_PI(?,?)', [$purchaseInvoiceHeader->PI_ID, $purchaseInvoiceHeader->PI_CURR]);
        } else {
            DB::connection('mysql')->select('CALL SP_INSERT_GL_PI(?,?)', [$purchaseInvoiceHeader->PI_ID, $purchaseInvoiceHeader->PI_CURR]);
        }
        Notification::send($purchaseInvoiceHeader->approver, new SupplyChainManagementApprovalNotification('App\Notifications\PIApprovalNoty', $purchaseInvoiceHeader));
        // Mail::to($purchaseInvoiceHeader->creator->email)->send(new PurhcaseInvoiceApprovalMail($PurchaseInvoiceHeader));
        Mail::to(env('TEST_MAIL_RECIPIENT'))->send(new PurhcaseInvoiceApprovalMail($purchaseInvoiceHeader));
    }

    public function updateToInProgress(): void
    {
        PurchaseInvoiceHeader::where('PI_ID', $this->id)->update([
            'PI_UPD_BY' => $this->user_id,
            'PI_APV_BY' => $this->user_id,
            'PI_APV_DATE' => Carbon::now(),
            'PI_APV_REMARK' => $this->approve_remark,
            'PI_APV_STATUS' => $this->approve_status,
            'PI_STATUS' => 'IP',
            'PI_NOTY' => $this->notification_id,
        ]);
    }

    public function delete(?string $remark, ?int $staff_id, int $delete_user_id, bool $update_approve_information, bool $from_einvoice = false): void
    {
        $iddt = PurchaseInvoiceDetail::where('PI_ID', $this->id)->where('PI_SEQ_STATUS', '<>', 'D')->get();
        $grns = PurchaseInvoiceDetail::where('PI_ID', $this->id)->where('PI_SEQ_STATUS', '<>', 'D')->select('PI_SOU_NO')->groupBy('PI_SOU_NO')->pluck('PI_SOU_NO');
        $data = [
            'PI_STATUS' => 'D',
            'PI_DEL_REMARK' => $remark,
            'deleted_at' => Carbon::now(),
            'PI_UPD_BY' => $delete_user_id,
        ];
        if ($update_approve_information) {
            $data['PI_APV_BY'] = $this->user_id;
            $data['PI_APV_DATE'] = Carbon::now();
            $data['PI_APV_REMARK'] = $this->approve_remark;
            $data['PI_APV_STATUS'] = $this->approve_status;
            $data['PI_NOTY'] = $this->notification_id;
        }
        PurchaseInvoiceHeader::where('PI_ID', $this->id)
            ->update($data);

        PurchaseInvoiceDetail::where('PI_ID', $this->id)
            ->where('PI_SEQ_STATUS', '<>', 'D')
            ->update([
                'PI_SEQ_STATUS' => 'I',
                'deleted_at' => Carbon::now(),
                'PI_UPD_BY' => $delete_user_id,
            ]);

        foreach ($iddt as $dt) {
            $grndt = GoodReceiveNoteDetail::where('GRN_ID', $dt->PI_SOU_NO)
                ->where('GRN_SEQ', $dt->PI_SOU_SEQ)
                ->where('GRN_STK_CODE', $dt->PI_STK_CODE)
                ->where('GRN_SEQ_STATUS', '!=', 'D')
                ->first();
            $grndt->GRN_PICK_QTY -= min($dt->PI_QTY, $dt->PI_QTY);
            $grndt->GRN_SEQ_STATUS = 'A';
            $grndt->save();
        }

        GoodReceiveNoteHeader::whereIn('GRN_ID', $grns)->update([
            'GRN_STATUS' => 'A',
            'GRN_INV_NO' => null,
            'GRN_INV_DATE' => null,
        ]);
    }
}
