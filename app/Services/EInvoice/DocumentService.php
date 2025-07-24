<?php

namespace App\Services\EInvoice;

use App\Enums\DocumentSummaryStatus;
use App\Enums\DocumentType;
use App\Enums\SubmissionOverallStatus;
use App\Exceptions\DocumentRejectionException;
use App\Exceptions\DocumentSubmitFailedException;
use App\Enums\DocumentResponseStatus;
class DocumentService
{
    protected string $id;

    protected DocumentType $documentType;

    /**
     * Create a new service instance.
     *
     * @return void
     */
    public function __construct(
        string $id,
        DocumentType $documentType

    ) {
        $this->id = $id;
        $this->documentType = $documentType;
    }

    public static function searchDocument(array $params)
    {
        $lhdiApiClient = new LHDNApiClient();
        $response = $lhdiApiClient->searchDocument($params);
        $data = $response->json();
        if (!$response->successful()) {
            throw new \Exception(json_encode($data), code: 500);
        }
        return $data;
    }

    public static function getDocument(string $uuid)
    {
        $lhdiApiClient = new LHDNApiClient();
        $response = $lhdiApiClient->getDocument($uuid);
        $data = $response->json();
        if (!$response->successful()) {
            throw new \Exception(json_encode($data), code: 500);
        }
        return $data;
    }

    public static function getDocumentDetails($uuid)
    {
        $lhdnApiClient = new LHDNApiClient();
        $response = $lhdnApiClient->getDocumentDetails($uuid);
        $data = $response->json();
        if (!$response->successful()) {
            throw new \Exception(json_encode($data), code: 500);
        }
        return $data;
    }
    public static function getRecentDocument($params)
    {
        $lhdiApiClient = new LHDNApiClient();
        $response = $lhdiApiClient->getRecentDocuments($params);
        $data = $response->json();
        if (!$response->successful()) {
            throw new \Exception(json_encode($data), 500);
        }
        return $data;
    }
    /**
     * Example method for the service.
     *
     * @param  string  $message
     */
    public static function documentSubmission($uid)
    {
        $lhdiApiClient = new LHDNApiClient;
        $response = $lhdiApiClient->getDocumentSubmission($uid);
        $data = $response->json();
        if (!$response->successful()) {
            throw new \Exception(json_encode($data), 409);
        }
        $overall_status = SubmissionOverallStatus::fromStatus($data['overallStatus']);
        $document_summary_status = isset($data['documentSummary'][0]['status']) ? DocumentSummaryStatus::fromStatus($data['documentSummary'][0]['status']) : DocumentSummaryStatus::NONE;
        $summary = $data['documentSummary'][0] ?? null;
        return [
            'overall_status' => $overall_status,
            'document_summary_status' => $document_summary_status,
            'summary' => $summary,
        ];

    }

    public function submitDocument()
    {
        $eInvoiceTableService = new EInvoiceTableService($this->id);

        $eInvoiceHeader = $eInvoiceTableService->getHeader();
        $eInvoiceDetail = $eInvoiceTableService->getDetails();
        $eInvoiceJsonDocumentService = new EInvoiceJsonDocumentFormatter;
        $document = $eInvoiceJsonDocumentService->generateJsonDocument($eInvoiceHeader, $eInvoiceDetail, $this->documentType);

        $documentTransformer = new DocumentTransformer;
        $documentDigest = $documentTransformer->base64_encode($documentTransformer->hash($documentTransformer->json_encode($document)));

        $digitalSignerService = new DigitalSigner;

        $signedDocumentDigest = $digitalSignerService->signDocument($document);
        $hashCertificate = $digitalSignerService->signCertificate();
        $certificateDetails = $digitalSignerService->getCertificateDetails();
        $documentWithSignaturePart = $eInvoiceJsonDocumentService->appendSignatureToJsonDocument($document, $hashCertificate, $certificateDetails);
        $signatureProperties = $documentWithSignaturePart['Invoice'][0]['UBLExtensions'][0]['UBLExtension'][0]['ExtensionContent'][0]['UBLDocumentSignatures'][0]['SignatureInformation'][0]['Signature'][0]['Object'][0]['QualifyingProperties'][0];
        $hashedSignedProperties = $documentTransformer->base64_encode($documentTransformer->hash($documentTransformer->json_encode($signatureProperties)));
        $certificateRawData = $digitalSignerService->getCertificateRawData();
        $signedDocument = $eInvoiceJsonDocumentService->generateSignedDocument($documentWithSignaturePart, $signedDocumentDigest, $certificateRawData, $hashedSignedProperties, $documentDigest, $certificateDetails);
        $jsonDocument = $documentTransformer->json_encode($signedDocument);
        $base64Document = $documentTransformer->base64_encode($jsonDocument);
        $hashDocument = $documentTransformer->hash($jsonDocument, false);
        $submissionData = [
            'documents' => [
                [
                    'format' => 'JSON',
                    'document' => $base64Document,
                    'documentHash' => $hashDocument,
                    'codeNumber' => $eInvoiceHeader->EINV_ID,
                ],
            ],
        ];
        $lhdiApiClient = new LHDNApiClient;
        $response = $lhdiApiClient->submitDocument($submissionData);
        $data = $response->json();
        if (!$response->successful()) {
            $statusCode = $response->status();
            $error = $data['error'] ?? $data['message'];
            throw new DocumentSubmitFailedException($error, $statusCode);
        }
        if (count($data['rejectedDocuments'])) {
            throw new DocumentRejectionException($data['rejectedDocuments'][0]['error'], 400);
        }

        return [
            'success' => true,
            'uid' => $data['submissionUid'],
        ];
    }

    public static function calculateDocumentStatus(
        SubmissionOverallStatus $overall_status,
        DocumentSummaryStatus $document_summary_status
    ) {
        $success = true;
        $status = null;
        $message = null;
        if ($overall_status == SubmissionOverallStatus::INVALID || $document_summary_status == DocumentSummaryStatus::INVALID) {
            $success = false;
            $status = DocumentResponseStatus::INVALID;
            $message = 'Invalid';

        }

        if ($overall_status == SubmissionOverallStatus::PARTIALLY_VALID) {
            $success = false;
            $status = DocumentResponseStatus::PARTIALLY_VALID;
            $message = 'Partially Invalid';
        }

        if ($document_summary_status == DocumentSummaryStatus::CANCELLED) {
            $success = false;
            $status = DocumentResponseStatus::CANCELLED;
            $message = 'Partially Invalid';
        }
        return [
            'success' => $success,
            'status' => $status,
            'message' => $message,
        ];
    }
}
