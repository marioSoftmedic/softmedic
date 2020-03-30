<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as NotificationsResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;


class ResetPassword extends NotificationsResetPassword
{


    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = url(config('app.client_url').'/password/reset/'.$this->token).'?email='.urlencode($notifiable->email);

        return (new MailMessage)
                    ->line('Haz recibido este mail, puesto que hemos recibido una solicitud de reset de password para tu cuenta')
                    ->action('Reiniciar Password', $url)
                    ->line('Si no haz pedido este requerimiento, omite este email');
    }


}
