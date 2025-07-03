<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\DocumentResponseStatus;
use App\Enums\DocumentSummaryStatus;
use App\Enums\SubmissionOverallStatus;
use App\Exceptions\DocumentSubmitFailedException;
use App\Http\Controllers\Controller;
use App\Http\Requests\DeleteDocumentRequest;
use App\Http\Requests\DocumentRequest;
use App\Http\Responses\DocumentResponse;
use App\Http\Responses\ErrorResponse;
use App\Services\EInvoice\DocumentService;
use App\Services\EInvoice\EInvoiceTableService;
use App\Services\EInvoice\InsertionService;
use App\Services\EInvoice\PrefixService;
use App\Exceptions\DocumentRejectionException;
use App\Services\HandlerService;
use \Illuminate\Database\QueryException;
use Exception;

class DocumentController extends Controller
{
    public function store(DocumentRequest $request)
    {

        $id = $request->input('id');

        $eInvoiceTableService = new EInvoiceTableService($id);

        try {
            $documentType = (new PrefixService($id))->getDocumentType();
            $handler = (new HandlerService(
                $documentType,
                $id,
                $request->input('user_id'),
                $request->input('approve_status'),
                $request->input('approve_remark'),
                (int) $request->input(key: 'notification_id')
            ))
                ->getHandle();
            $success = true;
            if ($request->input('approve_status') == 'A') {
                if ($request->input('is_einvoice_required')) {
                    $documentService = new DocumentService($id, $documentType);
                    $insertionService = new InsertionService($id, $documentType, $handler);
                    $insertionService->insertToEInvoiceTables();

                    $response = $documentService->submitDocument();
                    $uid = $response['uid'];
                    sleep(seconds: 2);
                    $submissionResponse = $documentService->documentSubmission($uid);
                    $overall_status = $submissionResponse['overall_status'];
                    $document_summary_status = $submissionResponse['document_summary_status'];
                    $summary = $submissionResponse['summary'];
                    if ($summary == null || $overall_status == SubmissionOverallStatus::IN_PROGRESS || $document_summary_status == DocumentSummaryStatus::Submitted) {
                        $eInvoiceTableService->markAsInProgress($uid);
                        $handler->updateToInProgress();
                        return new DocumentResponse(200, $success, DocumentResponseStatus::IN_PROGRESS);
                    }

                    $eInvoiceTableService->updateHeaderFromEinvoiceApi($summary, $uid, $overall_status, $document_summary_status);

                    $documentStatus = DocumentService::calculateDocumentStatus($overall_status, $document_summary_status);
                    $success = $documentStatus['success'];
                    if (!$success) {
                        $handler->delete(
                            "E-Invoice status is" . $documentStatus['message'],
                            null,
                            config('constants.SYSTEM_USER_ID'),
                            true,
                            false
                        );
                        return new DocumentResponse(200, $success, $documentStatus['status']);
                    }

                }
            }
            $handler->update(false);

            if ($request->input('approve_status') == 'A') {
                return new DocumentResponse(200, $success, DocumentResponseStatus::APPROVE);
            } else {
                return new DocumentResponse(200, $success, DocumentResponseStatus::REJECT);
            }
        } catch (DocumentSubmitFailedException $e) {
            $eInvoiceTableService->delete();
            return new ErrorResponse(
                http_code: $e->getCode(),
                message: $e->getMessage(),
                details: $e->getSubmitFailedDocumentErrorInformations()
            );
        } catch (DocumentRejectionException $e) {
            $eInvoiceTableService->delete();
            return new ErrorResponse(
                http_code: $e->getCode(),
                message: $e->getMessage(),
                details: $e->getRejectedDocumentErrorInformations()
            );

        } catch (QueryException $e) {
            $error = $e->errorInfo[2] ?? 'An unexpected database error occurred.';
            return new ErrorResponse(
                http_code: 400,
                message: $error,
                details: [],
            );
        } catch (Exception $e) {
            \Log::info($e);
            $statusCode = (int) $e->getCode();
            if ($statusCode < 100 || $statusCode > 599) {
                $statusCode = 500;
            }
            return new ErrorResponse((int) $statusCode, $e->getMessage(), []);
        }

    }

    public function delete(DeleteDocumentRequest $request)
    {
        try {
            $id = $request->input('id');
            $documentType = (new PrefixService($id))->getDocumentType();
            $handler = (new HandlerService(
                $documentType,
                $id,
                $request->input('user_id'),
                null,
                null,
                null
            ))
                ->getHandle();

            $handler->delete(
                $request->input('remark'),
                $request->input('staff_id'),
                $request->input('user_id'),
                false,
                false
            );

            return new DocumentResponse(200, true, DocumentResponseStatus::DELETED);
        } catch (Exception $e) {
            $statusCode = (int) $e->getCode();
            if ($statusCode < 100 || $statusCode > 599) {
                $statusCode = 500;
            }

            return new ErrorResponse((int) $statusCode, $e->getMessage(), []);
        }
    }
}
