<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class StorageWarningNotification extends Notification
{
    use Queueable;

    public function __construct(private float $percent)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $percent = number_format($this->percent, 2);

        return [
            'message' => 'Estas usando el '.$percent.'% del almacenamiento. Considera borrar archivos o subir tu plan.',
            'url' => route('teacher.assets.index'),
        ];
    }
}
