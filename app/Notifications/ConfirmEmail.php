<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;

class ConfirmEmail extends Notification
{
    use Queueable;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        Log::info('Generando enlace con token: ' . $this->token);
        return (new MailMessage)
                    ->subject('Confirma tu correo electrónico')
                    ->line('Haz clic en el botón para verificar tu cuenta.')
                    ->action('Confirmar Correo', route('email.confirm', ['token' => $this->token]))
                    ->line('Si no creaste esta cuenta, ignora este correo.');
    }
}
