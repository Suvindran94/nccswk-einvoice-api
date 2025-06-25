<?php

namespace App\Exceptions;

use Exception;

class DocumentRejectionException extends Exception
{
    protected $error;

    public function __construct(array $error, int $code = 400, \Throwable $previous = null)
    {
        $this->error = $error;
        $message = $this->formatRejectedDocumentsMessage($error);
        parent::__construct($message, $code, $previous);
    }


    protected function formatRejectedDocumentsMessage(array $error): string
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
            return "Document rejection failed with folloing issues: " . implode(" , ", $errors);
        }
        return "Document rejection failed with unspecified errors.";
    }

    public function getRejectedDocumentErrorInformations(): array
    {
        return $this->error;
    }

}
