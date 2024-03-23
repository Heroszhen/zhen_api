<?php

namespace App\Service\Mailer;

use App\Entity\Email\Config;
use App\Entity\Email\Email;
use App\Interfaces\MailerInterface;
use App\Service\UtilService;
use Doctrine\ORM\EntityManagerInterface;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
use Psr\Log\LoggerInterface;
use Twig\Environment;

class SmtpService implements MailerInterface
{
    private UtilService $utilService;
    private LoggerInterface $logger;
    private EntityManagerInterface $manager;
    private Environment $twig;

    public function __construct(
        UtilService $utilService,
        LoggerInterface $logger,
        EntityManagerInterface $manager,
        Environment $twig
    )
    {
        $this->utilService = $utilService;
        $this->logger = $logger;
        $this->manager = $manager;
        $this->twig = $twig;
    }

    public function sendEmail(Email $email): void
    {
        if (!$this->utilService->strContains($email->getEmailType(), 'smtp')) {
            return;
        }

        $mail = new PHPMailer(true);
        $conf = $this->manager->getRepository(Config::class)->findOneBy(['emailType' => $email->getEmailType()]);
        if (!$conf instanceof Config) {
            $this->logger->error("smtp email : Email type does not exist");

            return;
        }

        try {
            $mail->isSMTP();
            $mail->SMTPDebug = SMTP::DEBUG_OFF;
            $mail->Host = $conf->getHost();
            $mail->Port = 587;
            $mail->SMTPAuth = true;
            $mail->Username = $conf->getUsername();
            $mail->Password = $conf->getPassword();
            if (null === $conf->getFromEmailName()) {
                $mail->setFrom($conf->getFromEmail());
            } else {
                $mail->setFrom($conf->getFromEmail(), $conf->getFromEmailName());
            }
            $toEmail = $email->getToEmail();
            if (isset($toEmail['name'])) {
                $mail->addAddress($toEmail['email'], $toEmail['name']);
            } else {
                $mail->addAddress($toEmail['email']);
            }
            foreach($email->getCcEmail() as $cc) {
                if (isset($cc['name'])) {
                    $mail->addCc($cc['email'], $cc['name']);
                } else {
                    $mail->addCc($cc['email']);
                }
            }
            $mail->isHTML(true);
            $mail->CharSet = "UTF-8";
            $mail->Encoding = 'base64';
            $mail->Subject = $email->getTitle();
            $htmlContents = $this->twig->render('email/index.html.twig', [
                'content' => $email->getContent()
            ]);
            $mail->Body = $htmlContents;
            /*
            if($stream == false){
                foreach($files as $key=>$val){
                    $mail->addStringAttachment($val[1], $val[0]);
                }
            }else{
                foreach($files as $key=>$val){
                    $mail->addAttachment($val);
                } 
            }
*/
            if (!$mail->send()) {
                $this->logger->error("smtp email : {$mail->ErrorInfo}");
            }
        } catch (Exception $e) {
            $this->logger->error("smtp email : {$e->errorMessage()}");
        }
    }
}