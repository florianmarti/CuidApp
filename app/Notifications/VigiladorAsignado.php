<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VigiladorAsignado extends Notification
{
    use Queueable;
    protected $contrato;
    /**
     * Create a new notification instance.
     */
    public function __construct($contrato)
    {
        $this->contrato = $contrato;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('¡Buenas noticias! Se ha asignado un vigilador a tu zona.')
                    ->line('Vigilador: ' . $this->contrato->vigilador->name)
                    ->line('Fecha de inicio: ' . $this->contrato->start_date)
                    ->action('Ver detalles', url('/dashboard'))
                    ->line('Gracias por confiar en Cuidapp.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
