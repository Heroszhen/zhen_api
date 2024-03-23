<?php

namespace App\Interfaces;

use App\Entity\Email\Email;

interface MailerInterface
{
    public function sendEmail(Email $email): void;
}