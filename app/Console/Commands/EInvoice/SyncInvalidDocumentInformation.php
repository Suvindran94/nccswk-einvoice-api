<?php

namespace App\Console\Commands\EInvoice;

use Illuminate\Console\Command;
use App\Services\EInvoice\DocumentService;
use App\Models\EinvoiceHeader;
use App\Models\InvalidEinvoiceHeader;
use App\Models\InvalidEinvoiceValidationStep;
use App\Models\InvalidEinvoiceValidationStepError;
use App\Models\InvalidEinvoiceValidationStepInnerError;
class SyncInvalidDocumentInformation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'einvoice:sync-invalid-doc-inf';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync invalid documents information to our e-invoice header and e-invoice details.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $einvoiceHeader = EinvoiceHeader::select(
            'EINV_HDR.EINV_ID',
            'EINV_HDR.EINV_VALIDATE_UUID',
        )
            ->doesntHave('InvalidEinvoiceHeader')
            ->where('EINV_STATUS', 'I')
            ->orderBy('ID', 'DESC')
            ->get();

        foreach ($einvoiceHeader as $index => $hdr) {
            $detail = DocumentService::getDocumentDetails($hdr['EINV_VALIDATE_UUID']);

            $invalidEinvoiceHeaderId = InvalidEinvoiceHeader::insertGetId([
                'EINV_ID' => $hdr['EINV_ID'],
                'EINV_VALIDATE_UUID' => $hdr['EINV_VALIDATE_UUID']
            ]);
            $validationSteps = $detail['validationResults']['validationSteps'];
            foreach ($validationSteps as $step) {
                $status = $step['status'];
                if ($status != "Invalid")
                    continue;
                $name = $step['name'];
                $error = $step['error'];
                $innerErrors = $error['innerError'];
                $invalidEinvoiceValidationStepId = InvalidEinvoiceValidationStep::insertGetId([
                    'INVALID_EINV_HDR_ID' => $invalidEinvoiceHeaderId,
                    'EINV_VALIDATION_STEP_NAME' => $name
                ]);
                $invalidEinvoiceValidationStepErrorId = InvalidEinvoiceValidationStepError::insertGetId([
                    'INVALID_EINV_VALIDATION_STEP_ID' => $invalidEinvoiceValidationStepId,
                    'EINV_VALIDATION_STEP_ERROR_CODE' => $error['errorCode'],
                    'EINV_VALIDATION_STEP_ERROR_NAME' => $error['error'],
                ]);
                foreach ($innerErrors as $innerError) {
                    InvalidEinvoiceValidationStepInnerError::create([
                        'INVALID_EINV_VALIDATION_STEP_ERROR_ID' => $invalidEinvoiceValidationStepErrorId,
                        'EINV_PROPERTY_NAME' => $innerError['propertyName'],
                        'EINV_PROPERTY_PATH' => $innerError['propertyPath'],
                        'EINV_ERROR_CODE' => $innerError['errorCode'],
                        'EINV_ERROR_DESC' => $innerError['error'],
                        'EINV_INNER_ERROR' => !empty($innerError['innerError']) ? json_encode($innerError['innerError']) : null,
                    ]);
                }

            }
        }

    }
}
