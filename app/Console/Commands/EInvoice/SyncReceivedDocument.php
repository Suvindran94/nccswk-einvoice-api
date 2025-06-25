<?php

namespace App\Console\Commands\EInvoice;

use Illuminate\Console\Command;
use App\Services\EInvoice\DocumentService;
use App\Models\EinvoiceReceiveInformation;
use Carbon\Carbon;
class SyncReceivedDocument extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'einvoice:sync-rec-doc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize documents issued by others with the e-invoice received information table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $pageNo = 1;
        $totalPages = 1;
        $receivedDocumentsUuid = [];
        do {
            $params = [
                'pageSize' => 50,
                'pageNo' => $pageNo,
                'InvoiceDirection' => 'Received'
            ];
            $response = DocumentService::getRecentDocument($params);

            $totalPages = $response['metadata']['totalPages'];
            $currentPageUuids = array_map(function ($item) {
                return $item['uuid'];
            }, $response['result']);
            $receivedDocumentsUuid = [...$receivedDocumentsUuid, ...$currentPageUuids];
            $pageNo++;
        } while ($pageNo <= $totalPages);

        $existReceivedDocumentsUuid = EinvoiceReceiveInformation::select(
            'EINV_VALIDATE_UUID'
        )
            ->whereIn('EINV_VALIDATE_UUID', $receivedDocumentsUuid)
            ->pluck('EINV_VALIDATE_UUID')
            ->toArray();
        $receivedDocumentsUuidToProcess = array_diff($receivedDocumentsUuid, $existReceivedDocumentsUuid);
        foreach ($receivedDocumentsUuidToProcess as $uuid) {
            $information = DocumentService::getDocument($uuid);
            EinvoiceReceiveInformation::create([
                'EINV_VALIDATE_UUID' => $uuid,
                'EINV_SUBMISSION_UID' => $information['submissionUid'],
                'EINV_LONG_ID' => $information['longID'],
                'EINV_REC_ID' => $information['internalId'],
                'EINV_TYPE' => $information['typeName'],
                'EINV_TYPE_VERSION' => $information['typeVersionName'],
                'EINV_ISSUER_TIN' => $information['issuerTin'],
                'EINV_ISSUER_NAME' => $information['issuerName'],
                'EINV_RECEIVER_ID' => $information['receiverId'],
                'EINV_RECEIVER_NAME' => $information['receiverName'],
                'EINV_RECEIVED_DATETIME' => $information['dateTimeReceived'] ? Carbon::parse($information['dateTimeReceived'], config('services.einvoice.timezone'))->setTimeZone(config('app.timezone')) : null,
                'EINV_VALIDATE_DATETIME' => $information['dateTimeValidated'] ? Carbon::parse($information['dateTimeValidated'], config('services.einvoice.timezone'))->setTimeZone(config('app.timezone')) : null,
                'EINV_STATUS' => $information['status'],
                'EINV_DOC_STATUS_REASON' => $information['documentStatusReason'],
                'EINV_CANCEL_DATETIME' => $information['cancelDateTime'] ? Carbon::parse($information['cancelDateTime'], config('services.einvoice.timezone'))->setTimeZone(config('app.timezone')) : null,
                'EINV_REJECT_REQ_DATETIME' => $information['rejectRequestDateTime'] ? Carbon::parse($information['rejectRequestDateTime'], config('services.einvoice.timezone'))->setTimeZone(config('app.timezone')) : null,
                'EINV_DOCUMENT' => $information['document'],
                'EINV_CREATE_BY_USERID' => $information['createdByUserId'],
                'EINV_ISSUE_DATETIME' => $information['dateTimeIssued'] ? Carbon::parse($information['dateTimeIssued'], config('services.einvoice.timezone'))->setTimeZone(config('app.timezone')) : null,
                'EINV_TOTAL_EXCL_TAX' => $information['totalExcludingTax'],
                'EINV_TOTAL_DISC' => $information['totalDiscount'],
                'EINV_TOTAL_NET_AMT' => $information['totalNetAmount'],
                'EINV_TOTAL_PAYABLE_AMT' => $information['totalPayableAmount'],
            ]);
        }

    }
}
