<?php

namespace App\Notifications;

use App\Channels\SalesManagementChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SalesManagementApprovalNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected string $type;
    public function __construct(protected object $model)
    {
        $this->type = "App\Notifications\SalesInvoiceApproval";
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
        return [SalesManagementChannel::class];
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
        return [
            'created_by' => $this->model->creator->fname,
            'updated_by' => $this->model->updater->fname,
            'approved_by' => $this->model->approver->fname,
            'si_id' => $this->model->SI_ID,
            'created_date' => $this->model->SI_CREATE_DATE,
            'updated_date' => $this->model->SI_UPD_DATE,
            'approved_date' => $this->model->SI_APV_DATE,
            'ar_noty' => $this->model->SI_NOTY,
            'module' => 'AR'
        ];
    }
}
