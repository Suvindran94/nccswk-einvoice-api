<?php

namespace App\Console\Commands\EInvoice;

use App\Services\EInvoice\DocumentService;
use App\Services\EInvoice\EInvoiceTableService;

use Illuminate\Console\Command;
use App\Enums\SubmissionOverallStatus;
use App\Enums\DocumentSummaryStatus;
use Exception;
use App\Services\EInvoice\PrefixService;
use App\Services\HandlerService;
use App\Models\User;
class CheckDocumentStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'einvoice:check-status {--limit=100 : The maximum number of invoices to check in one run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks the status of e-invoice submissions from the Malaysia e-invoice system.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting e-invoice status check...');
        $limit = $this->option('limit');
        $inProgressDocuments = EInvoiceTableService::getInProgressHeader($limit);
        foreach ($inProgressDocuments as $document) {
            $id = $document->EINV_ID;
            try {
                $response = DocumentService::documentSubmission($document->EINV_SUBMISSION_UID);
                $overall_status = $response['overall_status'];
                $document_summary_status = $response['document_summary_status'];
                $summary = $response['summary'];

                if ($summary != null && $overall_status != SubmissionOverallStatus::IN_PROGRESS && $document_summary_status != DocumentSummaryStatus::Submitted) {
                    $eInvoiceTableService = new EInvoiceTableService($id);
                    $eInvoiceTableService->updateHeaderFromEinvoiceApi($summary, $document->EINV_SUBMISSION_UID, $overall_status, $document_summary_status);
                    $documentStatus = DocumentService::calculateDocumentStatus($overall_status, $document_summary_status);
                    $documentType = (new PrefixService($id))->getDocumentType();
                    $handler = (new HandlerService(
                        $documentType,
                        $id,
                        1,
                        'A',
                        null,
                        null
                    ))->getHandle();
                    if (!$documentStatus['success']) {
                        $default_user = User::find(config('constants.SYSTEM_USER_ID'));
                        $handler->delete(
                            "E-Invoice status is " . $documentStatus['message'],
                            $default_user->StaffID,
                            $default_user->id,
                            false,
                            true
                        );
                        return;
                    }
                    $handler->update(true);
                }
            } catch (Exception $e) {
                \Log::info($e->getMessage());
            }
        }
    }
}
