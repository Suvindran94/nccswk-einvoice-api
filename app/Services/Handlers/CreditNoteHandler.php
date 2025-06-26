<?php

namespace App\Services\Handlers;

use DB;
use Carbon\Carbon;
use App\Contracts\EInvoiceInsertHandlerInterface;
use App\Models\DcnHeader;
use App\Models\DcnDetail;
use App\Models\DcnHistory;
use App\Models\DcnDocument;
use Illuminate\Support\Facades\Notification;
use App\Notifications\FinanceManagementApprovalNotification;
use Exception;
use Illuminate\Database\QueryException;
class CreditNoteHandler implements EInvoiceInsertHandlerInterface
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
            DB::statement("insert into {$this->schema_fm}.EINV_HDR ( EINV_ID, EINV_SUP_NAME, EINV_BUY_NAME, EINV_SUP_TIN, EINV_SUP_REG_TYPE, EINV_SUP_SSM, EINV_SUP_ROC, 
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
                select  DCN_HDR.DCN_ID, CO_NAME1, AR_NAME1, CO_TIN, CO_REG_TYPE, CO_ROG, CO_ROG_NEW, 
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
                    if(DCN_HDR.DCN_OPT = 'CN','02','03') as EINV_TYPE,
                    ifnull((select ifnull(EINV_ID, '-') from {$this->schema_fm}.EINV_HDR 
                    where EINV_ID = (select DISTINCT DCN_SOU_ID from {$this->schema_fm}.DCN_DT where DCN_DT.DCN_SEQ_STATUS <> 'D' and  DCN_DT.deleted_at is null and DCN_DT.DCN_ID = DCN_HDR.DCN_ID) 
                    and EINV_HDR.EINV_OVERALL_STATUS = 'Valid' and EINV_HDR.EINV_VALIDATE_STATUS = 'Valid'), '-')  as EINV_DOC_REF_ID, 
                    ifnull((select ifnull(EINV_VALIDATE_UUID, 'NA') from {$this->schema_fm}.EINV_HDR 
                    where EINV_ID = (select DISTINCT DCN_SOU_ID from {$this->schema_fm}.DCN_DT where DCN_DT.DCN_SEQ_STATUS <> 'D' and  DCN_DT.deleted_at is null and DCN_DT.DCN_ID = DCN_HDR.DCN_ID) 
                    and EINV_HDR.EINV_OVERALL_STATUS = 'Valid' and EINV_HDR.EINV_VALIDATE_STATUS = 'Valid'), 'NA') as EINV_UIN_REF, 
                    now() as EINV_DATE_TIME,
                    DCN_HDR.DCN_CURR, 
                    DCN_HDR.LVC_CURR_RATE,
                    AR_BANK, 'AR_SI' as EINV_REMARK_MODULE,
                    AR_INV_TITLE,
                    null as EINV_FREQ,	
                    null as EINV_PERIOD,	
                    null as EINV_PAYMENT_MODE,	
                    null as EINV_SUP_BANK_ACCT, 
                    (select TERM_DESC from {$this->schema_sm}.MTN_TERM where MTN_TERM.TERM_ID = DCN_HDR.DCN_TERM) as EINV_TERM,
                    0 as EINV_PREPAYMENT_AMT, null as EINV_PREPAYMENT_DATE, '-' as EINV_PREPAYMENT_REF, 
                    null as EINV_BILL_REF, null as EINV_REF_CUSTOM, 
                    AR_NAME1 as EINV_SHIP_RCPT_NAME, 
                    concat(trim(ifnull(AR_ADDR1,'')), ' ', trim(ifnull(AR_ADDR2, '')),  trim(concat(' ', trim(ifnull(AR_ADDR3, '')), ' ' , trim(ifnull(AR_ADDR4, '')), ' ')), 
                        ' ', trim(ifnull(AR_MST_CUSTOMER.POSTCODE,'')), ', ', if((select CITY_NAME from {$this->schema_sm}.MTN_CITY where ID = AR_MST_CUSTOMER.CITY_ID) = 'NONE', '', 
                                                                                        concat((select trim(CITY_NAME) from {$this->schema_sm}.MTN_CITY where ID = AR_MST_CUSTOMER.CITY_ID),', ')),
                        if((select MTN_STATE.STATE_NAME  from {$this->schema_sm}.MTN_STATE where ID = AR_MST_CUSTOMER.STATE_ID ) = 'NONE', '',
                            concat((select trim(MTN_STATE.STATE_NAME) from {$this->schema_sm}.MTN_STATE where ID = AR_MST_CUSTOMER.STATE_ID ),', ')),
                        trim((select MTN_COUNTRY.COUNTRY_NAME from {$this->schema_sm}.MTN_COUNTRY where COUNTRY_ISO_CODE = AR_MST_CUSTOMER.COUNTRY_ID) ),
                        '. ') as EINV_SHIP_RCPT_ADDR, 
                    AR_ADDR1 as EINV_SHIP_RCPT_ADDR0, AR_ADDR2 as EINV_SHIP_RCPT_ADDR1, concat(trim(ifnull(AR_ADDR3, '')), ' ' , trim(ifnull(AR_ADDR4, ''))) as EINV_SHIP_RCPT_ADDR2, 
                    ifnull(AR_MST_CUSTOMER.POSTCODE,'') as EINV_SHIP_RCPT_POSTCODE, 
                    (select MTN_CITY.CITY_NAME from {$this->schema_sm}.MTN_CITY where ID = AR_MST_CUSTOMER.CITY_ID) as EINV_SHIP_RCPT_CITY, 
                    (select MTN_STATE.EINV_STATE_CODE from {$this->schema_sm}.MTN_STATE where ID = AR_MST_CUSTOMER.STATE_ID ) as EINV_SHIP_RCPT_STATE_ID,
                    (select MTN_COUNTRY.EINV_COUNTRY_CODE from {$this->schema_sm}.MTN_COUNTRY where COUNTRY_ISO_CODE = AR_MST_CUSTOMER.COUNTRY_ID) as EINV_SHIP_RCPT_COUNTRY_ID, 
                    EINV_TIN as EINV_SHIP_RCPT_TIN, AR_ROC_NEW as EINV_SHIP_RCPT_ROC, 'NA' as EINV_INCOTERMS, 
                    '-' as EINV_FNL_DEST,
                    DCN_NOTES as EINV_D_REMARK,
                    null as EINV_FTA, null as EINV_AUTH_CERT, '-' as EINV_REF_CUSTOM_2, 0 as EINV_DETAIL_OTHERS_AMT, null as EINV_DETAIL_OTHERS_REASON, 
                    0 as EINV_TOTAL_ADD_DISC_AMT, null as EINV_TOTAL_ADD_DISC_REASON, 0 as EINV_TOTAL_ADD_FEE_AMT, null as EINV_TOTAL_ADD_FEE_REASON, 
                    DCN_HDR.DCN_ROUNDING as EINV_ROUNDING_AMT, DCN_HDR.DCN_TAX_CHAR as EINV_TAX_CHAR, 0 as EINV_HAND_CHAR, 
                    0 as EINV_TRANS_CHAR, 0 as EINV_INSR_CHAR, 0 as EINV_OTH_CHAR, 
                    DCN_HDR.DCN_AMT as EINV_TOTAL_AMT, 
                    (select upper(users.name) from {$this->schema_admin}.users where id = DCN_HDR.DCN_CREATE_BY) as EINV_CREATE_BY_NAME, 
                    DCN_HDR.DCN_CREATE_BY as EINV_CREATE_BY, now() as EINV_CREATE_DATE, DCN_HDR.DCN_UPD_BY as EINV_UPD_BY, DCN_HDR.DCN_UPD_DATE as EINV_UPD_DATE, 
                    'P' as EINV_STATUS 
                from {$this->schema_sm}.MTN_CO, {$this->schema_fm}.DCN_HDR, {$this->schema_sm}.AR_MST_CUSTOMER
                    where DCN_HDR.DCN_AR = AR_MST_CUSTOMER.AR_ID
                    and DCN_ID = :id;", ['id' => $this->id]);

            DB::statement("insert into {$this->schema_fm}.EINV_DT ( EINV_ID, EINV_SEQ, EINV_SOU_NO, EINV_SOU_SEQ, EINV_SEQ_STATUS, EINV_CLASSIFICATION, EINV_PRODUCT_REF1, EINV_PRODUCT_REF2, 
                        EINV_PRODUCT_DESC, EINV_PRODUCT_REMARKS, EINV_UNIT_PRICE, EINV_NETT_UNIT_PRICE, EINV_TAX_TYPE, EINV_TAX_RATE, EINV_TAX_AMT, 
                        EINV_TAX_EXEMPTION_DESC, EINV_TAX_AMT_EXEMPTED, EINV_SUBTOTAL, EINV_TOTAL_EXCL_TAX, EINV_TOTAL_INCL_TAX, EINV_TOTAL_NET_AMT, 
                        EINV_TOTAL_PAYABLE_AMT, EINV_TOTAL_TAX_AMT_PER_TAX_TYPE, EINV_QTY, EINV_UOM, EINV_UOM_ID, EINV_DISC_RATE, EINV_DISC_AMT, 
                        EINV_DISC_REASON, EINV_FEE_RATE, EINV_FEE_AMT, EINV_FEE_REASON, EINV_PROD_TARIFF_CODE, EINV_COUNTRY_OF_ORI, 
                        EINV_CREATE_BY, EINV_CREATE_DATE, EINV_UPD_BY, EINV_UPD_DATE )

                select DCN_HDR.DCN_ID, DCN_DT.DCN_SEQ, GROUP_CONCAT(distinct DCN_HDR.DCN_DO_ID) as EINV_SOU_NO, DCN_DT.DCN_SOU_SEQ as EINV_SOU_SEQ,
                    'A' as EINV_SEQ_STATUS, EINV_CLASSIF_INCOME, DCN_DT.DCN_STK_CODE as EINV_PRODUCT_REF1, 
                    DCN_DT.DCN_GL_CODE as EINV_PRODUCT_REF2, concat(DCN_DT.DCN_GL_DESC_1, ' ', ifnull(DCN_DT.DCN_GL_DESC_2,'') ) as EINV_PRODUCT_DESC, 
                    DCN_DT.DCN_SEQ_REMARK as EINV_PRODUCT_REMARKS, 
                    DCN_DT.DCN_UNIT_PRICE as EINV_UNIT_PRICE, DCN_DT.DCN_NU_PRICE as EINV_NETT_UNIT_PRICE, 
                    (select MTN_GST_CODE.EINV_TAX_TYPE_CODE from {$this->schema_sm}.MTN_GST_CODE where MTN_GST_CODE.GST_ID = DCN_HDR.DCN_TAX_ID) as EINV_TAX_TYPE, 
                    (select MTN_GST_CODE.GST_RATE from {$this->schema_sm}.MTN_GST_CODE where MTN_GST_CODE.GST_ID = DCN_HDR.DCN_TAX_ID) as EINV_TAX_RATE, 
                    DCN_DT.DCN_GST_AMT as EINV_TAX_AMT, null as EINV_TAX_EXEMPTION_DESC, 0 as EINV_TAX_AMT_EXEMPTED, 
                    DCN_DT.DCN_AMT as EINV_SUBTOTAL, DCN_DT.DCN_AMT as EINV_TOTAL_EXCL_TAX, DCN_DT.DCN_AMT as EINV_TOTAL_INCL_TAX, DCN_DT.DCN_AMT as EINV_TOTAL_NET_AMT, 
                    DCN_DT.DCN_AMT as EINV_TOTAL_PAYABLE_AMT, 0 as EINV_TOTAL_TAX_AMT_PER_TAX_TYPE, 
                    DCN_DT.DCN_QTY as EINV_QTY, DCN_DT.DCN_UOM as EINV_UOM, 
                    (select  EINV_CODE from {$this->schema_sm}.MTN_MST where CLASS_ID = 'STK_UOM' and MTN_ID = DCN_DT.DCN_UOM) as EINV_UOM_ID, 
                    0 as EINV_DISC_RATE, 0 as EINV_DISC_AMT, null as EINV_DISC_REASON, 0 as EINV_FEE_RATE, 0 as EINV_FEE_AMT, null as EINV_FEE_REASON, 
                    (select distinct MTN_P_CAT.P_CAT_TARIFF from {$this->schema_sm}.MTN_P_CAT, {$this->schema_sm}.STK_MST 
                        where MTN_P_CAT.P_STK_CAT1 = STK_MST.STK_CAT1 and MTN_P_CAT.P_CAT_STATUS = 'A' 
                        and MTN_P_CAT.deleted_at is null and STK_MST.STK_CODE = DCN_DT.DCN_STK_CODE ) as EINV_PROD_TARIFF_CODE,
                     null as EINV_COUNTRY_OF_ORI, 
                    DCN_DT.DCN_CREATE_BY as EINV_CREATE_BY, DCN_HDR.DCN_CREATE_DATE as EINV_CREATE_DATE, DCN_DT.DCN_UPD_BY as EINV_UPD_BY, DCN_HDR.DCN_UPD_DATE as EINV_UPD_DATE
                from {$this->schema_fm}.DCN_DT, {$this->schema_fm}.DCN_HDR
                    where DCN_HDR.DCN_ID = DCN_DT.DCN_ID
                    and DCN_DT.DCN_ID = :id
                    and DCN_DT.deleted_at is null
                group by DCN_HDR.DCN_ID, DCN_DT.DCN_SEQ, DCN_SOU_SEQ,
                    EINV_CLASSIF_INCOME, DCN_DT.DCN_STK_CODE, 
                    DCN_DT.DCN_GL_CODE, concat(DCN_DT.DCN_GL_DESC_1, ' ', ifnull(DCN_DT.DCN_GL_DESC_2,'') ), 
                    DCN_DT.DCN_SEQ_REMARK, 
                    DCN_DT.DCN_UNIT_PRICE, DCN_DT.DCN_NU_PRICE, 
                    DCN_HDR.DCN_TAX_ID, DCN_DT.DCN_GST_AMT, DCN_DT.DCN_AMT, 
                    DCN_DT.DCN_QTY, DCN_DT.DCN_UOM, 
                    DCN_DT.DCN_CREATE_BY, DCN_HDR.DCN_CREATE_DATE, DCN_DT.DCN_UPD_BY, DCN_HDR.DCN_UPD_DATE;", ['id' => $this->id]);
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
        $dcnHeader = DcnHeader::where('DCN_ID', $this->id)->first();
        \Log::info($dcnHeader);
        $data = [
            'DCN_STATUS' => $this->approve_status,
        ];
        if (!$is_cron_job) {
            $data['DCN_UPD_BY'] = $this->user_id;
            $data['DCN_APV_BY'] = $this->user_id;
            $data['DCN_APV_DATE'] = Carbon::now();
            $data['DCN_APV_STATUS'] = $this->approve_status;
            $data['DCN_APV_REMARK'] = $this->approve_remark;
            $data['DCN_NOTY'] = $this->notification_id;
        }
        $dcnHeader->update($data);
        $dcnHeader->refresh();
        if ($this->approve_status == 'A') {
            DB::select('CALL SP_INSERT_GL_DCN(?)', [$dcnHeader->DCN_ID]);
        }
        $seq = DcnHistory::where('DCN_ID', $dcnHeader->DCN_ID)->count();
        DcnHistory::insert([
            'DCN_ID' => $dcnHeader->DCN_ID,
            'DCN_APV_SEQ' => $seq + 1,
            'DCN_ACTION' => $this->approve_status,
            'DCN_APV_BY' => $dcnHeader->DCN_APV_BY,
            'DCN_APV_DATE' => $dcnHeader->DCN_APV_DATE,
            'DCN_APV_REMARK' => $dcnHeader->DCN_APV_REMARK,
            'DCN_CREATE_BY' => $dcnHeader->DCN_UPD_BY,
            'DCN_UPD_BY' => $dcnHeader->DCN_UPD_BY,
        ]);
        Notification::send($dcnHeader->creator, new FinanceManagementApprovalNotification($dcnHeader));

    }

    public function updateToInProgress(): void
    {
        DcnHeader::where('DCN_ID', $this->id)->update([
            'DCN_UPD_BY' => $this->user_id,
            'DCN_APV_BY' => $this->user_id,
            'DCN_APV_DATE' => Carbon::now(),
            'DCN_APV_REMARK' => $this->approve_remark,
            'DCN_APV_STATUS' => $this->approve_status,
            'DCN_STATUS' => 'IP',
            'DCN_NOTY' => $this->notification_id,
        ]);
    }

    public function delete(?string $remark, ?int $staff_id, int $delete_user_id, bool $update_approve_information): void
    {
        $dcnHeader = DcnHeader::where('DCN_ID', $this->id)->first();
        if ($dcnHeader->DCN_APV_STATUS == 'A') {
            DB::select('CALL SP_INSERT_GL_DCN_DEL(?)', array($dcnHeader->DCN_ID));
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
        $dcnHeader->update($data);

        DcnDetail::where('DCN_ID', $this->id)
            ->update([
                'DCN_SEQ_STATUS' => 'I',
                'DCN_UPD_BY' => $delete_user_id,
                'deleted_at' => Carbon::now(),
            ]);
        DcnDocument::where('DCN_ID', $this->id)
            ->update([
                'STATUS' => 'I',
                'UPD_BY' => $delete_user_id,
                'deleted_at' => Carbon::now(),
            ]);
    }
}
