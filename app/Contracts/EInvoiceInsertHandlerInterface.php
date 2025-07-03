<?php

namespace App\Contracts;

interface EInvoiceInsertHandlerInterface
{
    public function insertToEInvoiceTables(): void;

    public function update(bool $is_cron_job): void;

    public function updateToInProgress(): void;

<<<<<<< HEAD
    public function delete(?string $remark, ?int $staff_id, int $delete_user_id, bool $update_approve_information, bool $from_einvoice): void;
=======
    public function delete(?string $remark, ?int $staff_id, int $delete_user_id, bool $update_approve_information, bool $from_einvoice = false): void;
>>>>>>> 31ee59d4799acbe5da1baa47096b34a754c70b29

}

