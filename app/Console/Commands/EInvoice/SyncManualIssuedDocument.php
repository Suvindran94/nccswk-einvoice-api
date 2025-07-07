<?php

namespace App\Console\Commands\EInvoice;

use App\Services\EInvoice\DocumentService;
use App\Services\EInvoice\ManualIssueDocumentProcessor;
use App\Models\ManualIssueEinvoiceHeader;
use Carbon\Carbon;
use App\Enums\SubmissionChannel;
use Illuminate\Console\Command;
use App\Services\GeneralService;
class SyncManualIssuedDocument extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'einvoice:sync-man-iss-doc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize manual issued documents to relative table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('start');
        $pageNo = 1;
        $totalPages = 1;
        $index = 0;
        $manualIssueDocumentsUuid = [];
        do {
            $params = [
                'submissionDateFrom' => Carbon::now()->setTimezone(config('services.einvoice.timezone'))->startOfMonth()->subDays(10)->format('Y-m-d') . 'T' . Carbon::now()->setTimezone(config('services.einvoice.timezone'))->startOfMonth()->subDays(10)->format('H:i:s') . 'Z',
                'submissionDateTo' => Carbon::now()->setTimezone(config('services.einvoice.timezone'))->subSeconds(1)->format('Y-m-d') . 'T' . Carbon::now()->setTimezone(config('services.einvoice.timezone'))->subSeconds(1)->format('H:i:s') . 'Z',
                'pageSize' => 50,
                'pageNo' => $pageNo,
            ];
            $response = DocumentService::searchDocument($params);
            $documents = $response['result'];
            $totalPages = $response['metadata']['totalPages'];
            foreach ($documents as $doc) {
                $submissionChannel = SubmissionChannel::fromSubmissionChannel($doc['submissionChannel']);
                $einvoiceId = $doc['internalId'];
                if (!GeneralService::isValidEInvoiceId($einvoiceId)) {
                    continue;
                }
                if ($submissionChannel == SubmissionChannel::INVOICING_PORTAL || $submissionChannel == SubmissionChannel::INVOICING_MOBILE_APP) {
                    $manualIssueDocumentsUuid[] = $doc['uuid'];
                }
            }
            $pageNo++;
        } while ($pageNo <= $totalPages);
        $existManualIssueDocumentsUuid = ManualIssueEinvoiceHeader::select(
            'EINV_VALIDATE_UUID'
        )
            ->whereIn('EINV_VALIDATE_UUID', $manualIssueDocumentsUuid)
            ->pluck('EINV_VALIDATE_UUID')
            ->toArray();
        $manualIssueDocumentsUuid = array_diff($manualIssueDocumentsUuid, $existManualIssueDocumentsUuid);


        foreach ($manualIssueDocumentsUuid as $index => $uuid) {
            $information = DocumentService::getDocument($uuid);
            $manualIssueDocumentProcessor = new ManualIssueDocumentProcessor($information);
            $handler = $manualIssueDocumentProcessor->getHandler();
            $handler->process();
        }
    }
}
