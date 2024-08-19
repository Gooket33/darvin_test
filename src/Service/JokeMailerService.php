<?php
namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class JokeMailerService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendJokeEmail(string $recipientEmail, string $joke, string $category): void
    {
        $email = (new Email())
            ->from('no-reply@example.com')
            ->to($recipientEmail)
            ->subject("Случайная шутка из {$category}")
            ->text($joke);

        $this->mailer->send($email);
    }
}
