<?php


namespace App\Console\Commands\EInvoice;

use App\Models\EinvoiceHeader;
use App\Services\EInvoice\DocumentService;
use App\Models\User;
use App\Services\HandlerService;
use App\Services\EInvoice\PrefixService;
use Illuminate\Console\Command;
use App\Services\GeneralService;
use DB;
use Carbon\Carbon;
class SyncCancelDocument extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'einvoice:sync-cancel-doc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync canceled documents to our e-invoice header and e-invoice details.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('start');
        $cancelHoursDuration = 96;
        $params = [
            'InvoiceDirection' => 'Sent',
            'status' => 'Cancelled',
            'submissionDateFrom' => Carbon::now()->setTimezone(config('services.einvoice.timezone'))->subHours($cancelHoursDuration)->format('Y-m-d') . 'T' . Carbon::now()->setTimezone(config('services.einvoice.timezone'))->subHours($cancelHoursDuration)->format('H:i:s') . 'Z',
            'submissionDateTo' => Carbon::now()->setTimezone(config('services.einvoice.timezone'))->format('Y-m-d') . 'T' . Carbon::now()->setTimezone(config('services.einvoice.timezone'))->format('H:i:s') . 'Z',
        ];
        $response = DocumentService::getRecentDocument($params);
        $cancelDocuments = $response['result'];
        foreach ($cancelDocuments as $document) {
            $einvoiceId = $document['internalId'];

            if (!GeneralService::isValidEInvoiceId($einvoiceId)) {
                continue;
            }

            $uuid = $document['uuid'];
            $einvoiceHeader = EinvoiceHeader::where('EINV_VALIDATE_UUID', $uuid)
                ->where('EINV_VALIDATE_STATUS', '<>', 'Cancelled')
                ->where('EINV_VALIDATE_STATUS', '<>', 'Cancelled')
                ->first();
            if (empty($einvoiceHeader)) {
                continue;
            }
            $einvoiceHeader->update(attributes: [
                'EINV_VALIDATE_STATUS' => 'Cancelled',
                'EINV_OVERALL_STATUS' => 'Cancelled',
                'EINV_CANCEL_DATETIME' => Carbon::parse($document['cancelDateTime'], config('services.einvoice.timezone'))->setTimezone(config('app.timezone')),
                'EINV_DOC_STATUS_REASON' => $document['documentStatusReason'],
                'EINV_UPD_DATE' => Carbon::now(),
                'EINV_UPD_BY' => 1,
                'EINV_STATUS' => 'C'
            ]);
            $remark = 'Cancel Via E-Invoice';
            $prefixService = new PrefixService($einvoiceId);
            $default_user = User::find(config('constants.system_user_id'));
            $handlerService = new HandlerService($prefixService->getDocumentType(), $einvoiceId, $default_user->id, null, null, null);
            $handlerService->getHandle()->delete(
                $remark,
                $default_user->StaffID,
                $default_user->id,
                false,
                true
            );
            $prefix = substr(strtoupper($prefixService->getDocumentType()->value), 2);
            DB::connection('mysql')->statement('CALL SP_INSERT_EINV_CANCEL(?, ?, ?, ?, ?)', [strtoupper($prefix), $einvoiceId, $default_user->id, Carbon::now(), $remark]);

        }
    }
}
