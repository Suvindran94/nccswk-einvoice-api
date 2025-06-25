<?php

namespace App\Exceptions;

use Exception;

class DocumentSubmitFailedException extends Exception
{
    protected $error;
    protected array $details = [];
    public function __construct(array|string $error, int $code, \Throwable $previous = null)
    {
        $this->error = $error;
        if (($code == 400 || $code == 403 || $code == 422) && is_array($error)) {
            $detail = $error;
            $message = $this->formatErrorMessage($error);
        } else {
            $message = $error;
        }

        parent::__construct($message, $code, $previous);
    }

    protected function formatErrorMessage(array $error): string
    {
        $errors = [];
        $getOuterMessage = true;
        if (isset($error["details"])) {
            $details = $error['details'];
            foreach ($details as $index => $detail) {
                if ($detail['message']) {
                    $getOuterMessage = false;
                    $errors[] = $detail['message'];
                }
            }
        }
        if ($getOuterMessage) {
            $errors[] = $error['message'];
        }
        if (!empty($errors)) {
            return "Document submit failed with folloing issues: " . implode(" , ", $errors);
        }
        return "Document submit failed with unspecified errors.";
    }

    public function getSubmitFailedDocumentErrorInformations(): array
    {
        return $this->details;
    }

}
