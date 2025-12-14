<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DashboardNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $titulo,
        public string $mensaje,
        public ?string $url = null,
        public ?string $textoBoton = null,
        public ?int $carrera_id = null
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'titulo' => $this->titulo,
            'mensaje' => $this->mensaje,
            'url' => $this->url,
            'texto_boton' => $this->textoBoton,
            'carrera_id' => $this->carrera_id,
        ];
    }
}
