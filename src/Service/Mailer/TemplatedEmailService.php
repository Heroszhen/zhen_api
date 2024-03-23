<?php

namespace App\Service\Mailer;

use App\Entity\Email\Email;
use App\Interfaces\MailerInterface;

class TemplatedEmailService implements MailerInterface
{
    public function sendEmail(Email $email): void
    {
        if ($email->getEmailType() !== Email::EMAIL_TYPES_TEMPLATED_EMAIL_GMAIL) {
            return;
        }
    }
}
