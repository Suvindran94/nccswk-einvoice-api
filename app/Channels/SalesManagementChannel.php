<?php

namespace App\Channels;

use Illuminate\Notifications\Channels\DatabaseChannel as IlluminateDatabaseChannel;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use DB;
class SalesManagementChannel extends IlluminateDatabaseChannel
{
    protected $connection = 'mysql_sm';
    public function buildPayload($notifiable, Notification $notification)
    {
        return [
            'id' => (string) Str::uuid(),
            'type' => method_exists($notification, 'getType')
                ? $notification->getType() : get_class($notification),
            'module' => '',
            'notifiable_type' => 'App\User',
            'notifiable_id' => $notifiable->getKey(),
            'data' => json_encode($notification->toArray($notifiable)),
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function send($notifiable, Notification $notification): void
    {
        $data = $this->buildPayload($notifiable, $notification);
        DB::connection($this->connection)->table('notifications')->insert($data);
    }

}