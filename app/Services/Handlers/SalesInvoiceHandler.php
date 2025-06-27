<?php

namespace App\Services\Handlers;

use App\Models\DeliveryOrderDetail;
use App\Models\DeliveryOrderHeader;
use App\Models\SalesInvoiceDeposit;
use App\Models\SalesInvoiceDocument;
use Illuminate\Support\Facades\Notification;
;
use App\Notifications\SalesManagementApprovalNotification;
use DB;
use Carbon\Carbon;
use App\Contracts\EInvoiceInsertHandlerInterface;
use App\Models\SalesInvoiceHeader;
use App\Models\SalesInvoiceDetail;
use Exception;
use Illuminate\Database\QueryException;
class SalesInvoiceHandler implements EInvoiceInsertHandlerInterface
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
                select  SI_HDR.SI_ID, CO_NAME1, AR_NAME1, CO_TIN, CO_REG_TYPE, CO_ROG, CO_ROG_NEW, 
                    ifnull(CO_SST,'NA') as CO_SST, ifnull(CO_TT,'NA') as CO_TTX,  CO_EMAIL as EINV_SUP_EMAIL, CO_MSIC, CO_BUSINESS_DESC, CO_WEBSITE,
                    AR_ID, AR_NAMES, EINV_TIN, trim((select REG_TYPE from {$this->schema_sm}.MTN_REG_TYPE where ID = {$this->schema_sm}.AR_MST_CUSTOMER.EINV_REG_TYPE)), AR_ROC_NEW, ifnull(AR_SST_REG_NO,'NA') as AR_SST_REG_NO, 
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
                    (select MTN_COUNTRY.EINV_COUNTRY_CODE from {$this->schema_sm}.MTN_COUNTRY where COUNTRY_ISO_CODE = MTN_CO.COUNTRY_ID) as CO_IRB_COUNTRY,concat(trim(ifnull(AR_ADDR1,'')), ' ', trim(ifnull(AR_ADDR2, '')),  trim(concat(' ', trim(ifnull(AR_ADDR3, '')), ' ' , trim(ifnull(AR_ADDR4, '')), ' ')), 
                        ' ', trim(ifnull(AR_MST_CUSTOMER.POSTCODE,'')), ', ', if((select CITY_NAME from {$this->schema_sm}.MTN_CITY where ID = AR_MST_CUSTOMER.CITY_ID) = 'NONE', '', 
                                                                                        concat((select trim(CITY_NAME) from {$this->schema_sm}.MTN_CITY where ID = AR_MST_CUSTOMER.CITY_ID),', ')),
                        if((select MTN_STATE.STATE_NAME  from {$this->schema_sm}.MTN_STATE where ID = AR_MST_CUSTOMER.STATE_ID ) = 'NONE', '',
                            concat((select trim(MTN_STATE.STATE_NAME) from {$this->schema_sm}.MTN_STATE where ID = AR_MST_CUSTOMER.STATE_ID ),', ')),
                        trim((select MTN_COUNTRY.COUNTRY_NAME from {$this->schema_sm}.MTN_COUNTRY where COUNTRY_ISO_CODE = AR_MST_CUSTOMER.COUNTRY_ID) ),
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
                    SI_HDR.SI_SOURCE_ID as EINV_DOC_REF_ID, 
                    'NA' as EINV_UIN_REF, 
                    ifnull(SI_HDR.SI_APV_DATE, now()) as EINV_DATE_TIME,
                    SI_HDR.SI_CURR, 
                    SI_HDR.LVC_CURR_RATE,
                    AR_BANK, 'AR_SI' as EINV_REMARK_MODULE,
                    AR_INV_TITLE,
                    null as EINV_FREQ,	
                    null as EINV_PERIOD,	
                    null as EINV_PAYMENT_MODE,	
                    null as EINV_SUP_BANK_ACCT, 
                    (select TERM_DESC from {$this->schema_sm}.MTN_TERM where MTN_TERM.TERM_ID = SI_HDR.SI_TERM) as EINV_TERM,
                    ifnull(tblDeposit.depositAmt,0) as EINV_PREPAYMENT_AMT,if(ifnull(tblDeposit.depositAmt,0) = 0, null,  tblDeposit.DepositDate) as EINV_PREPAYMENT_DATE, 
                    IF(ifnull(tblDeposit.depositAmt,0) = 0, '-', ifnull(tblDeposit.DepositRef,'-')) as EINV_PREPAYMENT_REF,
                    null as EINV_BILL_REF, null as EINV_REF_CUSTOM, 
                    SI_D_NAME as EINV_SHIP_RCPT_NAME, 
                    concat(trim(ifnull(SI_D_LINE_1,'')), ' ', trim(ifnull(SI_D_LINE_2, '')),  trim(concat(' ', trim(ifnull(SI_D_LINE_3, '')), ' ' , trim(ifnull(SI_D_LINE_4, '')), ' ')), 
                        ' ', trim(ifnull(SI_D_POSTCODE,'')), ' ', 
                        trim(if((select CITY_NAME from {$this->schema_sm}.MTN_CITY where ID = SI_D_CITY_ID) = 'NONE', '', 
                                (select CITY_NAME from {$this->schema_sm}.MTN_CITY where ID = SI_D_CITY_ID)) ),
                        ', ', trim((select MTN_STATE.STATE_NAME  from {$this->schema_sm}.MTN_STATE where ID = SI_D_STATE_ID) ),
                        ', ', trim((select MTN_COUNTRY.COUNTRY_NAME from {$this->schema_sm}.MTN_COUNTRY where COUNTRY_ISO_CODE = SI_D_COUNTRY_ID) ),
                        '. ') as EINV_SHIP_RCPT_ADDR, 
                    SI_D_LINE_1 as EINV_SHIP_RCPT_ADDR0, SI_D_LINE_2 as EINV_SHIP_RCPT_ADDR1, concat(trim(ifnull(SI_D_LINE_3, '')), ' ' , trim(ifnull(SI_D_LINE_4, ''))) as EINV_SHIP_RCPT_ADDR2, 
                    ifnull(SI_D_POSTCODE,'') as EINV_SHIP_RCPT_POSTCODE, 
                    (select MTN_CITY.CITY_NAME from {$this->schema_sm}.MTN_CITY where ID = SI_D_CITY_ID) as EINV_SHIP_RCPT_CITY, 
                    (select MTN_STATE.EINV_STATE_CODE from {$this->schema_sm}.MTN_STATE where ID = SI_D_STATE_ID ) as EINV_SHIP_RCPT_STATE_ID,
                    (select MTN_COUNTRY.EINV_COUNTRY_CODE from {$this->schema_sm}.MTN_COUNTRY where COUNTRY_ISO_CODE = SI_D_COUNTRY_ID) as EINV_SHIP_RCPT_COUNTRY_ID, 
                    EINV_TIN as EINV_SHIP_RCPT_TIN, AR_ROC_NEW as EINV_SHIP_RCPT_ROC, 
                    AR_MST_CUSTOMER.AR_INCOTERM as EINV_INCOTERMS, 
                    SI_FNL_DEST as EINV_FNL_DEST,
                    SI_D_REMARK as EINV_D_REMARK,
                    null as EINV_FTA, null as EINV_AUTH_CERT, 
                    (select ifnull(DOC_REF, '-') from {$this->schema_sm}.SI_DOC where SI_ID = SI_HDR.SI_ID and  SI_DOC_TYPE = '1' and deleted_at is null ) as EINV_REF_CUSTOM_2, 
                    0 as EINV_DETAIL_OTHERS_AMT, null as EINV_DETAIL_OTHERS_REASON, 
                    0 as EINV_TOTAL_ADD_DISC_AMT, null as EINV_TOTAL_ADD_DISC_REASON, 
                    (SI_HAND_CHAR + SI_TRANS_CHAR + SI_INSR_CHAR + SI_OTH_CHAR) as EINV_TOTAL_ADD_FEE_AMT, 
                    concat_ws(', ', IF(SI_HAND_CHAR > 0 , 'HANDLING', null),  IF(SI_TRANS_CHAR > 0 , 'FREIGHT', null), IF(SI_INSR_CHAR > 0 , 'INSURANCE', null)  , IF(SI_OTH_CHAR > 0 , 'OTHER CHARGES', null) ) as EINV_TOTAL_ADD_FEE_REASON, 
                    SI_HDR.SI_ROUNDING as EINV_ROUNDING_AMT, SI_TAX_CHAR as EINV_TAX_CHAR, SI_HAND_CHAR as EINV_HAND_CHAR, 
                    SI_TRANS_CHAR as EINV_TRANS_CHAR, SI_INSR_CHAR as EINV_INSR_CHAR, SI_OTH_CHAR as EINV_OTH_CHAR, 
                    (SI_HDR.SI_AMT - ifnull(tblDeposit.depositAmt,0)) as EINV_TOTAL_AMT, 
                    (select upper(users.name) from {$this->schema_admin}.users where id = SI_HDR.SI_CREATE_BY) as EINV_CREATE_BY_NAME, 
                    SI_HDR.SI_CREATE_BY as EINV_CREATE_BY, SI_HDR.SI_APV_DATE as EINV_CREATE_DATE, SI_HDR.SI_UPD_BY as EINV_UPD_BY, SI_HDR.SI_UPD_DATE as EINV_UPD_DATE, 
                    'P' as EINV_STATUS 
                from {$this->schema_sm}.MTN_CO, {$this->schema_sm}.AR_MST_CUSTOMER, {$this->schema_sm}.SI_HDR 
                left join ( select SI_DEPOSIT.SI_ID, group_concat(SI_DEPOSIT.QUO_ID) as DepositRef, max(RECEIPT_HDR.RCPT_COLLECTION_DATE) as DepositDate , sum(SI_DEPOSIT.DEP_AMT_OFFSET) as depositAmt 
                            from {$this->schema_sm}.SI_DEPOSIT, {$this->schema_fm}.RECEIPT_HDR
                            where SI_DEPOSIT.RCPT_ID = RECEIPT_HDR.RCPT_ID 
                            and SI_DEPOSIT.deleted_at is null 
                            group by SI_DEPOSIT.SI_ID
                        ) tblDeposit on tblDeposit.SI_ID = SI_HDR.SI_ID
                where SI_HDR.SI_AR = AR_MST_CUSTOMER.AR_ID
                and SI_HDR.SI_ID = :id
            ;", ['id' => $this->id]);

            DB::statement("
                insert into {$this->schema_fm}.EINV_DT ( EINV_ID, EINV_SEQ, EINV_SOU_NO, EINV_SOU_SEQ, EINV_SEQ_STATUS, EINV_CLASSIFICATION, EINV_PRODUCT_REF1, EINV_PRODUCT_REF2, 
                    EINV_PRODUCT_DESC, EINV_PRODUCT_REMARKS,EINV_PO_NO, EINV_UNIT_PRICE, EINV_NETT_UNIT_PRICE, EINV_TAX_TYPE, EINV_TAX_RATE, EINV_TAX_AMT, 
                    EINV_TAX_EXEMPTION_DESC, EINV_TAX_AMT_EXEMPTED, EINV_SUBTOTAL, EINV_TOTAL_EXCL_TAX, EINV_TOTAL_INCL_TAX, EINV_TOTAL_NET_AMT, 
                    EINV_TOTAL_PAYABLE_AMT, EINV_TOTAL_TAX_AMT_PER_TAX_TYPE, EINV_QTY, EINV_UOM, EINV_UOM_ID, EINV_DISC_RATE, EINV_DISC_AMT, 
                    EINV_DISC_REASON, EINV_FEE_RATE, EINV_FEE_AMT, EINV_FEE_REASON, EINV_PROD_TARIFF_CODE, EINV_COUNTRY_OF_ORI, 
                    EINV_CREATE_BY, EINV_CREATE_DATE, EINV_UPD_BY, EINV_UPD_DATE )
                select TblA.SI_ID, @ronum:=@ronum + 1 as SI_SEQ, TblA.EINV_SOU_NO, null as EINV_SOU_SEQ, TblA.EINV_SEQ_STATUS, TblA.EINV_CLASSIF_INCOME, 
                    TblA.EINV_PRODUCT_REF1, TblA.EINV_PRODUCT_REF2, TblA.EINV_PRODUCT_DESC, TblA.EINV_PRODUCT_REMARKS,TblA.EINV_PO_NO, 
                    TblA.EINV_UNIT_PRICE, TblA.EINV_NETT_UNIT_PRICE, TblA.EINV_TAX_TYPE, TblA.EINV_TAX_RATE, TblA.EINV_TAX_AMT, 
                    TblA.EINV_TAX_EXEMPTION_DESC, TblA.EINV_TAX_AMT_EXEMPTED, TblA.EINV_SUBTOTAL, TblA.EINV_TOTAL_EXCL_TAX, 
                    TblA.EINV_TOTAL_INCL_TAX, TblA.EINV_TOTAL_NET_AMT, TblA.EINV_TOTAL_PAYABLE_AMT, TblA.EINV_TOTAL_TAX_AMT_PER_TAX_TYPE, 
                    TblA.EINV_QTY, TblA.EINV_UOM, TblA.EINV_UOM_ID, TblA.EINV_DISC_RATE, TblA.EINV_DISC_AMT, TblA.EINV_DISC_REASON, 
                    TblA.EINV_FEE_RATE, TblA.EINV_FEE_AMT, TblA.EINV_FEE_REASON, TblA.EINV_PROD_TARIFF_CODE, TblA.EINV_COUNTRY_OF_ORI, 
                    TblA.EINV_CREATE_BY, TblA.EINV_CREATE_DATE, TblA.EINV_UPD_BY, TblA.EINV_UPD_DATE 
                from 
                (
                select SI_HDR.SI_ID, SI_SOU_ID as EINV_SOU_NO, 
                    SI_SEQ_STATUS as EINV_SEQ_STATUS, EINV_CLASSIF_INCOME, SI_DT.SI_CUST_STK_CODE as EINV_PRODUCT_REF1, 
                    SI_DT.SI_STK_CODE as EINV_PRODUCT_REF2, concat(SI_DT.SI_STK_DESC_1, ' ', ifnull(SI_DT.SI_STK_DESC_2,'') ) as EINV_PRODUCT_DESC, 
                    SI_DT.SI_SEQ_REMARK as EINV_PRODUCT_REMARKS, SI_DT.SI_PO_NO as EINV_PO_NO,
                    SI_DT.SI_UNIT_PRICE as EINV_UNIT_PRICE, SI_DT.SI_NU_PRICE as EINV_NETT_UNIT_PRICE, 
                    (select MTN_GST_CODE.EINV_TAX_TYPE_CODE from {$this->schema_sm}.MTN_GST_CODE where MTN_GST_CODE.GST_ID = SI_HDR.SI_TAX_ID) as EINV_TAX_TYPE, 
                    (select MTN_GST_CODE.GST_RATE from {$this->schema_sm}.MTN_GST_CODE where MTN_GST_CODE.GST_ID = SI_HDR.SI_TAX_ID) as EINV_TAX_RATE, 
                    sum(SI_DT.SI_GST_AMT) as EINV_TAX_AMT, null as EINV_TAX_EXEMPTION_DESC, 0 as EINV_TAX_AMT_EXEMPTED, 
                    sum(SI_DT.SI_AMT) as EINV_SUBTOTAL, sum(SI_DT.SI_AMT) as EINV_TOTAL_EXCL_TAX, sum(SI_DT.SI_AMT) as EINV_TOTAL_INCL_TAX, sum(SI_DT.SI_AMT) as EINV_TOTAL_NET_AMT, 
                    sum(SI_DT.SI_AMT) as EINV_TOTAL_PAYABLE_AMT, 0 as EINV_TOTAL_TAX_AMT_PER_TAX_TYPE, 
                    sum(SI_DT.SI_QTY) as EINV_QTY, SI_DT.SI_UOM as EINV_UOM, 
                    (select  EINV_CODE from {$this->schema_sm}.MTN_MST where CLASS_ID = 'STK_UOM' and MTN_ID = SI_DT.SI_UOM) as EINV_UOM_ID, 
                    0 as EINV_DISC_RATE, 0 as EINV_DISC_AMT, null as EINV_DISC_REASON, 0 as EINV_FEE_RATE, 0 as EINV_FEE_AMT, null as EINV_FEE_REASON, 
                    (select distinct MTN_P_CAT.P_CAT_TARIFF from {$this->schema_sm}.MTN_P_CAT, {$this->schema_sm}.STK_MST 
                    where MTN_P_CAT.P_STK_CAT1 = STK_MST.STK_CAT1 and MTN_P_CAT.P_CAT_STATUS = 'A' 
                    and MTN_P_CAT.deleted_at is null and STK_MST.STK_CODE = SI_DT.SI_STK_CODE ) as EINV_PROD_TARIFF_CODE,
                     null as EINV_COUNTRY_OF_ORI, 
                    SI_DT.SI_CREATE_BY as EINV_CREATE_BY, SI_HDR.SI_CREATE_DATE as EINV_CREATE_DATE, SI_DT.SI_UPD_BY as EINV_UPD_BY, SI_HDR.SI_UPD_DATE as EINV_UPD_DATE
                from {$this->schema_sm}.SI_DT, {$this->schema_sm}.SI_HDR
                where SI_HDR.SI_ID = SI_DT.SI_ID
                and SI_DT.SI_ID = :id
                group by SI_HDR.SI_ID, 
                    EINV_CLASSIF_INCOME, SI_DT.SI_SOU_ID, SI_SEQ_STATUS,
                    SI_DT.SI_CUST_STK_CODE, SI_DT.SI_STK_CODE, concat(SI_DT.SI_STK_DESC_1, ' ', ifnull(SI_DT.SI_STK_DESC_2,'') ), 
                    SI_DT.SI_SEQ_REMARK, SI_DT.SI_PO_NO,
                    SI_DT.SI_UNIT_PRICE, SI_DT.SI_NU_PRICE, 
                    SI_HDR.SI_TAX_ID, SI_DT.SI_UOM, 
                    SI_DT.SI_DISC1, SI_DT.SI_CREATE_BY, SI_HDR.SI_CREATE_DATE, SI_DT.SI_UPD_BY, SI_HDR.SI_UPD_DATE
                ) TblA       
                JOIN (SELECT @ronum:=0) p; 
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
        $salesInvoiceHeader = SalesInvoiceHeader::where('SI_ID', $this->id)->first();
        $data = [
            'SI_STATUS' => $this->approve_status,
        ];
        if (!$is_cron_job) {
            $data['SI_UPD_BY'] = $this->user_id;
            $data['SI_APV_BY'] = $this->user_id;
            $data['SI_APV_DATE'] = Carbon::now();
            $data['SI_APV_STATUS'] = $this->approve_status;
            $data['SI_APV_REMARK'] = $this->approve_remark;
            $data['SI_NOTY'] = $this->notification_id;
        }
        $salesInvoiceHeader->update($data);
        $salesInvoiceHeader->refresh();
        \Log::info($salesInvoiceHeader);
        if ($this->approve_status == 'A') {
            DB::connection('mysql_admin')->select('CALL SP_INSERT_GL_SI(?)', [$salesInvoiceHeader->SI_ID]);
        }
        Notification::send($salesInvoiceHeader->creator, new SalesManagementApprovalNotification($salesInvoiceHeader));
    }

    public function updateToInProgress(): void
    {
        SalesInvoiceHeader::where('SI_ID', $this->id)->update([
            "SI_APV_BY" => $this->user_id,
            "SI_APV_DATE" => Carbon::now(),
            "SI_APV_REMARK" => $this->approve_remark,
            "SI_APV_STATUS" => $this->approve_status,
            "SI_NOTY" => $this->notification_id,
            "SI_STATUS" => 'IP',
            "SI_UPD_BY" => $this->user_id,
        ]);
    }

    public function delete(?string $remark, ?int $staff_id, int $delete_user_id, bool $update_approve_information): void
    {
        $salesInvoiceHeader = SalesInvoiceHEader::where('SI_ID', $this->id)->first();
        $doArray = !empty($salesInvoiceHeader) ? explode(',', $salesInvoiceHeader->SI_SOURCE_ID) : [];
        $data = [
            'SI_UPD_BY' => $delete_user_id,
            'SI_DELETED_BY' => $delete_user_id,
            'SI_STATUS' => 'D',
            'SI_DELETED_REMARK' => $remark,
            'deleted_at' => Carbon::now(),
        ];
        if ($update_approve_information) {
            $data['SI_APV_BY'] = $this->user_id;
            $data['SI_APV_DATE'] = Carbon::now();
            $data['SI_APV_REMARK'] = $this->approve_remark;
            $data['SI_APV_STATUS'] = $this->approve_status;
            $data['SI_NOTY'] = $this->notification_id;
        }
        $salesInvoiceHeader->update($data);

        SalesInvoiceDetail::where('SI_ID', $this->id)
            ->update([
                'SI_UPD_BY' => $delete_user_id,
                'SI_SEQ_STATUS' => 'I',
                'deleted_at' => Carbon::now(),
            ]);

        SalesInvoiceDocument::where('SI_ID', $this->id)
            ->update([
                'STATUS' => 'I',
                'deleted_at' => Carbon::now(),
                'deleted_by' => $delete_user_id,
                'UPD_BY' => $delete_user_id,
            ]);

        SalesInvoiceDeposit::where('SI_ID', $this->id)
            ->update([
                'STATUS' => 'I',
                'deleted_at' => Carbon::now(),
                'UPD_BY' => $delete_user_id,
            ]);

        if (!empty($doArray)) {
            DeliveryOrderHeader::whereIn('DO_ID', $doArray)
                ->where('DO_STATUS', 'C')
                ->update([
                    'DO_STATUS' => 'A',
                    'DO_UPD_BY' => $delete_user_id,
                ]);
            DeliveryOrderDetail::whereIn('DO_ID', $doArray)
                ->where('DO_SEQ_STATUS', 'C')
                ->update([
                    'DO_SEQ_STATUS' => 'A',
                    'DO_UPD_BY' => $delete_user_id,
                ]);
        }
    }
}
