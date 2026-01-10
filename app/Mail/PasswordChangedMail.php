<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Clients;

class PasswordChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $client;
    public $changedFields;

    /**
     * Create a new message instance.
     *
     * @param Clients $client
     * @param array $changedFields Массив измененных полей ['password', 'email']
     */
    public function __construct(Clients $client, array $changedFields = [])
    {
        $this->client = $client;
        $this->changedFields = $changedFields;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'Изменение данных в личном кабинете';
        
        if (in_array('password', $this->changedFields)) {
            $subject = 'Изменение пароля в личном кабинете';
        } elseif (in_array('email', $this->changedFields)) {
            $subject = 'Изменение email в личном кабинете';
        } elseif (in_array('phone', $this->changedFields)) {
            $subject = 'Изменение номера телефона в личном кабинете';
        }

        return $this->subject($subject)
            ->view('emails.password_changed')
            ->with([
                'client' => $this->client,
                'changedFields' => $this->changedFields,
            ]);
    }
}