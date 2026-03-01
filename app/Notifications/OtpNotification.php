<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OtpNotification extends Notification
{
    use Queueable;

    protected $otp;

    /**
     * Create a new notification instance.
     */
    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Kode OTP Verifikasi Login')
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Anda telah melakukan login menggunakan Google Account.')
            ->line('Gunakan kode OTP berikut untuk melanjutkan:')
            ->line('**' . $this->otp . '**')
            ->line('Kode OTP ini berlaku untuk satu kali penggunaan.')
            ->line('Jika Anda tidak melakukan login, abaikan email ini.')
            ->salutation('Terima kasih, ' . config('app.name'));
    }
}