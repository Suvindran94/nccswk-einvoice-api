<?php

namespace App\Notifications;

use App\Channels\SupplyChainManagementDatabaseChannel;
use App\Models\PurchaseInvoiceHeader;
use App\Models\SundryPurchaseInvoiceHeader;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SupplyChainManagementApprovalNotification extends Notification
{
    use Queueable;


    /**
     * Create a new notification instance.
     */
    public function __construct(protected string $type, protected object $model)
    {
    }

    public function getType()
    {
        return $this->type;
    }
    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [SupplyChainManagementDatabaseChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $array = [
            'created_by' => $this->model->creator->fname,
            'updated_by' => $this->model->updater->fname,
            'approved_by' => $this->model->approver->fname,
        ];

        if ($this->model instanceof PurchaseInvoiceHeader) {
            $array['created_date'] = $this->model->PI_CREATE_DATE;
            $array['updated_date'] = $this->model->PI_UPDATE_DATE;
            $array['approved_date'] = $this->model->PI_APV_DATE;
            $array['ap_noty'] = $this->model->PI_NOTY;
            $array['pi_id'] = $this->model->PI_ID;
        } else if ($this->model instanceof SundryPurchaseInvoiceHeader) {
            $array['created_date'] = $this->model->SPI_CREATE_DATE;
            $array['updated_date'] = $this->model->SPI_UPDATE_DATE;
            $array['approved_date'] = $this->model->SPI_APV_DATE;
            $array['ap_noty'] = $this->model->SPI_NOTY;
            $array['spi_id'] = $this->model->SPI_ID;
        }

        return $array;
    }
}
