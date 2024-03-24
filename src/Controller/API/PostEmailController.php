<?php

namespace App\Controller\API;

use App\Entity\Email\Email;
use App\Service\Mailer\SmtpService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class PostEmailController extends AbstractController
{
    private SmtpService $mailer;
    private ValidatorInterface $validator; 

    public function __construct(
        SmtpService $mailer,
        ValidatorInterface $validator
    )
    {
        $this->mailer = $mailer;
        $this->validator = $validator;
    }

    /**
     * @return Email|JsonResponse
     */
    public function __invoke(Request $request)
    {
        $content = json_decode($request->getContent(), true);
        $email = new Email();
        $email
            ->setToEmail($content['toEmail'])
            ->setTitle($content['title'])
            ->setContent($content['content'])
            ->setEmailType($content['emailType'])
        ;
        if (isset($content['ccEmail'])) {
            $email->setCcEmail($content['ccEmail']);
        }

        $errors = $this->validator->validate($email);
        if (0 !== $errors->count()) {
            return $email;
        } 

        $this->mailer->sendEmail($email);

        return $this->json([
            "@context" => "/api/contexts/Email",
            "@type" => "Email",
            "@id" => "/api/emails",
        ]);
    }
}
