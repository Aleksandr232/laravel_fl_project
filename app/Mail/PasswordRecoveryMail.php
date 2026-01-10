<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Clients;

class PasswordRecoveryMail extends Mailable
{
    use Queueable, SerializesModels;

    public $client;
    public $newPassword;

    /**
     * Create a new message instance.
     *
     * @param Clients $client
     * @param string $newPassword Новый сгенерированный пароль
     */
    public function __construct(Clients $client, string $newPassword)
    {
        $this->client = $client;
        $this->newPassword = $newPassword;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Восстановление пароля - АСТ Компонентс')
            ->view('emails.password_recovery')
            ->with([
                'client' => $this->client,
                'newPassword' => $this->newPassword,
            ]);
    }
}
