<?php

namespace App\Notifications;

use App\Channels\FinanceManagementDatabaseChannel;
use Illuminate\Notifications\Notification;

class FinanceManagementApprovalNotification extends Notification
{

    /**
     * Create a new notification instance.
     */
    protected string $type = "App\Notifications\ApprovalNotification";
    public function __construct(
        protected object $model
    ) {
        //
    }
    public function viaConnections(): array
    {
        return [
            'database' => 'mysql_fm',
        ];
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
        return [FinanceManagementDatabaseChannel::class];
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
            'created_date' => $this->model->createdDate,
            'updated_date' => $this->model->updatedDate,
            'approved_date' => $this->model->approvedDate,
            'fm_noty' => $this->model->notification_id,
            'fm_id' => $this->model->modelId
        ];
    }
}
