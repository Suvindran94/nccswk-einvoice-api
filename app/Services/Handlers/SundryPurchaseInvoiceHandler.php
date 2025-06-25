<?php

namespace App\Services\Handlers;

use App\Mail\SundryPurchaseInvoiceApprovalMail;
use App\Notifications\SupplyChainManagementApprovalNotification;
use DB;
use Carbon\Carbon;
use App\Contracts\EInvoiceInsertHandlerInterface;
use App\Models\SundryPurchaseInvoiceHeader;
use App\Models\SundryPurchaseInvoiceDetail;
use App\Models\SundryGoodReceiveNoteDetail;
use App\Models\SundryGoodReceiveNoteHeader;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use Exception;
use Illuminate\Database\QueryException;

class SundryPurchaseInvoiceHandler implements EInvoiceInsertHandlerInterface
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
                select  SPI_HDR.SPI_ID, AP_NAME1, 
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
                    ifnull(SPI_HDR.SPI_REF,'NA') as EINV_DOC_REF_ID, 
                    'NA' as EINV_UIN_REF, 
                    now() as EINV_DATE_TIME,
                    SPI_HDR.SPI_CURR, 
                    SPI_HDR.LVC_CURR_RATE,
                    null as EINV_BUY_BANK_PAYABLE, 'AP_SPI' as EINV_REMARK_MODULE,
                    'SUNDRY PURCHASE INVOICE' as AP_INV_TITLE,
                    null as EINV_FREQ,	
                    null as EINV_PERIOD,	
                    null as EINV_PAYMENT_MODE,	
                    AP_BANK1 as EINV_SUP_BANK_ACCT, 
                    (select TERM_DESC from {$this->schema_sm}.MTN_TERM where MTN_TERM.TERM_ID = SPI_HDR.SPI_TERM) as EINV_TERM,
                    0 as EINV_PREPAYMENT_AMT, null as EINV_PREPAYMENT_DATE, '-' as EINV_PREPAYMENT_REF, 
                    SPI_HDR.SPI_INV_NO as EINV_BILL_REF, SPI_HDR.SPI_K1_ID as EINV_REF_CUSTOM, 
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
                    SPI_D_REMARK as EINV_D_REMARK,
                    null as EINV_FTA, null as EINV_AUTH_CERT, '-' as EINV_REF_CUSTOM_2, 0 as EINV_DETAIL_OTHERS_AMT, null as EINV_DETAIL_OTHERS_REASON, 
                    0 as EINV_TOTAL_ADD_DISC_AMT, null as EINV_TOTAL_ADD_DISC_REASON, 
                    (SPI_HAND_CHAR + SPI_TRANS_CHAR + SPI_INSR_CHAR + SPI_OTH_CHAR) as EINV_TOTAL_ADD_FEE_AMT, 
                    concat_ws(', ', IF(SPI_HAND_CHAR > 0 , 'HANDLING', null),  IF(SPI_TRANS_CHAR > 0 , 'FREIGHT', null), IF(SPI_INSR_CHAR > 0 , 'INSURANCE', null)  , IF(SPI_OTH_CHAR > 0 , 'OTHER CHARGES', null) ) as EINV_TOTAL_ADD_FEE_REASON, 
                    SPI_HDR.SPI_ROUNDING as EINV_ROUNDING_AMT, SPI_HDR.SPI_TAX_CHAR as EINV_TAX_CHAR, SPI_HDR.SPI_HAND_CHAR  as EINV_HAND_CHAR, 
                    SPI_HDR.SPI_TRANS_CHAR  as EINV_TRANS_CHAR, SPI_HDR.SPI_INSR_CHAR  as EINV_INSR_CHAR, SPI_HDR.SPI_OTH_CHAR  as EINV_OTH_CHAR, 
                    SPI_HDR.SPI_AMT as EINV_TOTAL_AMT, 
                    (select upper(users.name) from {$this->schema_admin}.users where id = SPI_HDR.SPI_CREATE_BY) as EINV_CREATE_BY_NAME, 
                    SPI_HDR.SPI_CREATE_BY as EINV_CREATE_BY, now() as EINV_CREATE_DATE, SPI_HDR.SPI_UPD_BY as EINV_UPD_BY, SPI_HDR.SPI_UPD_DATE as EINV_UPD_DATE, 
                    'P' as EINV_STATUS       
                from {$this->schema_sm}.MTN_CO, {$this->schema_scm}.SPI_HDR, {$this->schema_scm}.AP_MST_SUPPLIER
                where SPI_HDR.SPI_AP = AP_MST_SUPPLIER.AP_ID
                and SPI_ID = :id
                ;
            ", ['id' => $this->id]);
            DB::statement("
                insert into {$this->schema_fm}.EINV_DT ( EINV_ID, EINV_SEQ, EINV_SOU_NO, EINV_SOU_SEQ, EINV_SEQ_STATUS, EINV_CLASSIFICATION, EINV_PRODUCT_REF1, EINV_PRODUCT_REF2, 
                    EINV_PRODUCT_DESC, EINV_PRODUCT_REMARKS, EINV_UNIT_PRICE, EINV_NETT_UNIT_PRICE, EINV_TAX_TYPE, EINV_TAX_RATE, EINV_TAX_AMT, 
                    EINV_TAX_EXEMPTION_DESC, EINV_TAX_AMT_EXEMPTED, EINV_SUBTOTAL, EINV_TOTAL_EXCL_TAX, EINV_TOTAL_INCL_TAX, EINV_TOTAL_NET_AMT, 
                    EINV_TOTAL_PAYABLE_AMT, EINV_TOTAL_TAX_AMT_PER_TAX_TYPE, EINV_QTY, EINV_UOM, EINV_UOM_ID, EINV_DISC_RATE, EINV_DISC_AMT, 
                    EINV_DISC_REASON, EINV_FEE_RATE, EINV_FEE_AMT, EINV_FEE_REASON, EINV_PROD_TARIFF_CODE, EINV_COUNTRY_OF_ORI, 
                    EINV_CREATE_BY, EINV_CREATE_DATE, EINV_UPD_BY, EINV_UPD_DATE )
                select SPI_HDR.SPI_ID, SPI_DT.SPI_SEQ, GROUP_CONCAT(distinct SPI_DT.SPI_SOU_NO) as EINV_SOU_NO, SPI_SOU_SEQ as EINV_SOU_SEQ,
                    'A' as EINV_SEQ_STATUS, EINV_CLASSIF_EXPENSE, SPI_DT.SPI_GL_CODE as EINV_PRODUCT_REF1, 
                    null as EINV_PRODUCT_REF2, SPI_DT.SPI_GL_DESC_1 as EINV_PRODUCT_DESC, 
                    SPI_DT.SPI_SEQ_REMARK as EINV_PRODUCT_REMARKS, 
                    SPI_DT.SPI_UNIT_PRICE as EINV_UNIT_PRICE, SPI_DT.SPI_NU_PRICE as EINV_NETT_UNIT_PRICE, 
                    (select MTN_GST_CODE.EINV_TAX_TYPE_CODE from {$this->schema_sm}.MTN_GST_CODE where MTN_GST_CODE.GST_ID = SPI_DT.SPI_GST_ID) as EINV_TAX_TYPE, 
                    (select MTN_GST_CODE.GST_RATE from {$this->schema_sm}.MTN_GST_CODE where MTN_GST_CODE.GST_ID = SPI_DT.SPI_GST_ID) as EINV_TAX_RATE, 
                    SPI_DT.SPI_GST_AMT as EINV_TAX_AMT, null as EINV_TAX_EXEMPTION_DESC, 0 as EINV_TAX_AMT_EXEMPTED, 
                    SPI_DT.SPI_AMT as EINV_SUBTOTAL, SPI_DT.SPI_AMT as EINV_TOTAL_EXCL_TAX, SPI_DT.SPI_AMT as EINV_TOTAL_INCL_TAX, SPI_DT.SPI_AMT as EINV_TOTAL_NET_AMT, 
                    SPI_DT.SPI_AMT as EINV_TOTAL_PAYABLE_AMT, 0 as EINV_TOTAL_TAX_AMT_PER_TAX_TYPE, 
                    SPI_DT.SPI_QTY as EINV_QTY, SPI_DT.SPI_UOM as EINV_UOM, 
                    (select  EINV_CODE from {$this->schema_sm}.MTN_MST where CLASS_ID = 'STK_UOM' and MTN_ID = SPI_DT.SPI_UOM) as EINV_UOM_ID, 
                    0 as EINV_DISC_RATE, SPI_DT.SPI_DISC1 as EINV_DISC_AMT, null as EINV_DISC_REASON, 0 as EINV_FEE_RATE, 0 as EINV_FEE_AMT, null as EINV_FEE_REASON, 
                    null as EINV_PROD_TARIFF_CODE, null as EINV_COUNTRY_OF_ORI, 
                    SPI_DT.SPI_CREATE_BY as EINV_CREATE_BY, SPI_HDR.SPI_CREATE_DATE as EINV_CREATE_DATE, SPI_DT.SPI_UPD_BY as EINV_UPD_BY, SPI_HDR.SPI_UPD_DATE as EINV_UPD_DATE
                from {$this->schema_scm}.SPI_DT, {$this->schema_scm}.SPI_HDR
                    where SPI_HDR.SPI_ID = SPI_DT.SPI_ID
                    and SPI_DT.SPI_ID =  :id
                    and SPI_DT.deleted_at is null
                    group by SPI_HDR.SPI_ID, SPI_DT.SPI_SEQ, SPI_SOU_SEQ,
                    EINV_CLASSIF_EXPENSE, SPI_DT.SPI_GL_CODE, 
                    SPI_DT.SPI_GL_DESC_1, 
                    SPI_DT.SPI_SEQ_REMARK, 
                    SPI_DT.SPI_UNIT_PRICE, SPI_DT.SPI_NU_PRICE, 
                    SPI_DT.SPI_GST_ID, SPI_DT.SPI_GST_AMT, SPI_DT.SPI_AMT, 
                    SPI_DT.SPI_QTY, SPI_DT.SPI_UOM, 
                    SPI_DT.SPI_DISC1, SPI_DT.SPI_CREATE_BY, SPI_HDR.SPI_CREATE_DATE, SPI_DT.SPI_UPD_BY, SPI_HDR.SPI_UPD_DATE;
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
        $sundryPurchaseInvoiceHeader = SundryPurchaseInvoiceHeader::where('SPI_ID', $this->id)->first();
        $data = [
            'SPI_STATUS' => $this->approve_status == 'A' ? 'A' : 'N',
        ];
        if (!$is_cron_job) {
            $data['SPI_UPD_BY'] = $this->user_id;
            $data['SPI_APV_BY'] = $this->user_id;
            $data['SPI_APV_DATE'] = Carbon::now();
            $data['SPI_APV_STATUS'] = $this->approve_status;
            $data['SPI_APV_REMARK'] = $this->approve_remark;
            $data['SPI_NOTY'] = $this->notification_id;
        }
        $sundryPurchaseInvoiceHeader->update($data);
        $sundryPurchaseInvoiceHeader->refresh();
        if ($this->approve_status == 'A') {
            DB::select('CALL SP_INSERT_GL_SPI(?)', [$sundryPurchaseInvoiceHeader->SPI_ID]);
        }
        \Log::info(json_encode($sundryPurchaseInvoiceHeader->creator));
        Notification::send($sundryPurchaseInvoiceHeader->creator, new SupplyChainManagementApprovalNotification('App\Notifications\SPIApprovalNoty', $sundryPurchaseInvoiceHeader));
        // Mail::to($sundryPurchaseInvoiceHeader->creator->email)->send(new SundryPurchaseInvoiceApprovalMail($sundryPurchaseInvoiceHeader));
        Mail::to(env('TEST_MAIL_RECIPIENT'))->send(new SundryPurchaseInvoiceApprovalMail($sundryPurchaseInvoiceHeader));
    }

    public function updateToInProgress(): void
    {
        SundryPurchaseInvoiceHeader::where('SPI_ID', $this->id)->update([
            "SPI_APV_BY" => $this->user_id,
            "SPI_APV_DATE" => Carbon::now(),
            "SPI_APV_REMARK" => $this->approve_remark,
            "SPI_APV_STATUS" => $this->approve_status,
            "SPI_NOTY" => $this->notification_id,
            "SPI_STATUS" => 'IP',
            "SPI_UPD_BY" => $this->user_id,
        ]);
    }
    public function delete(?string $remark, ?int $staff_id, int $delete_user_id, bool $update_approve_information): void
    {
        $iddt = SundryPurchaseInvoiceDetail::where('SPI_ID', $this->id)->where('SPI_SEQ_STATUS', '<>', 'D')->get();
        $grns = SundryPurchaseInvoiceDetail::where('SPI_ID', $this->id)->where('SPI_SEQ_STATUS', '<>', 'D')->select('SPI_SOU_NO')->groupBy('SPI_SOU_NO')->pluck('SPI_SOU_NO');
        $data = [
            'SPI_STATUS' => 'D',
            'deleted_at' => Carbon::now(),
            'SPI_UPD_BY' => $delete_user_id,
        ];
        if ($update_approve_information) {
            $data['SPI_APV_BY'] = $this->user_id;
            $data['SPI_APV_DATE'] = Carbon::now();
            $data['SPI_APV_REMARK'] = $this->approve_remark;
            $data['SPI_APV_STATUS'] = $this->approve_status;
            $data['SPI_NOTY'] = $this->notification_id;
        }
        SundryPurchaseInvoiceHeader::where('SPI_ID', $this->id)
            ->update($data);

        SundryPurchaseInvoiceDetail::where('SPI_ID', $this->id)
            ->where('SPI_SEQ_STATUS', '<>', 'D')
            ->update([
                'SPI_SEQ_STATUS' => 'I',
                'deleted_at' => Carbon::now(),
                'SPI_UPD_BY' => $delete_user_id,
            ]);

        foreach ($iddt as $dt) {
            $grndt = SundryGoodReceiveNoteDetail::where('SGRN_ID', $dt->SPI_SOU_NO)
                ->where('SGRN_SEQ', $dt->SPI_SOU_SEQ)
                ->where('SGRN_GL_CODE', $dt->SPI_GL_CODE)
                ->where('SGRN_SEQ_STATUS', '!=', 'D')
                ->first();
            $grndt->SGRN_PICK_QTY -= min($dt->SPI_QTY, $dt->SPI_QTY);
            $grndt->SGRN_SEQ_STATUS = 'A';
            $grndt->save();
        }

        SundryGoodReceiveNoteHeader::whereIn('SGRN_ID', $grns)->update([
            'SGRN_STATUS' => 'A',
            'SGRN_UPD_BY' => $delete_user_id,
        ]);
    }
}
