<?php

namespace App\Entity\Email;

use App\Repository\Email\EmailRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Controller\API\Email\PostEmailController;
use App\Validator as AppAssert;

/**
 * @ORM\Entity(repositoryClass=EmailRepository::class)
 * @ApiResource(
 *     attributes={
 *         "access_control"="is_granted('ROLE_USER')",
 *         "denormalization_context"={"groups"={"input"}}
 *     },
 *     collectionOperations={
 *         "get"={},
 *         "post"={
 *             "method" = "POST",
 *             "controller" = PostEmailController::class,
 *             "deserialize"=false,
 *         }
 *     }
 * )
 * @AppAssert\CheckEmail
 */
class Email
{
    public const EMAIL_TYPES = [
        self::EMAIL_TYPES_SMTP_HOSTINGER,
        self::EMAIL_TYPES_TEMPLATED_EMAIL_GMAIL
    ];

    public const EMAIL_TYPES_SMTP_HOSTINGER = 'smtp_hostinger';
    public const EMAIL_TYPES_TEMPLATED_EMAIL_GMAIL = 'templated_email_gmail';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * ['email' => 'exemple@gmail.com', 'name' => 'Vincent']
     * @ORM\Column(type="array", nullable=true)
     */
    private $fromEmail = null;

    /**
     * ['email' => 'exemple@gmail.com', 'name' => 'Vincent']
     * @ORM\Column(type="array")
     * 
     * @Assert\NotBlank(allowNull=false)
     * @Groups({"input"})
     */
    private $toEmail = [];

    /**
     * [
     *  ['email' => 'exemple1@gmail.com', 'name' => 'Julien'],
     *  ['email' => 'exemple2@gmail.com', 'name' => 'Vincent']
     * ]
     * @ORM\Column(type="array", nullable=true)
     * @Groups({"input"})
     */
    private ?array $ccEmail = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(allowNull=false)
     * @Groups({"input"})
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank(allowNull=false)
     * @Groups({"input"})
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(allowNull=false)
     * @Groups({"input"})
     */
    private $emailType;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFromEmail(): ?array
    {
        return $this->fromEmail;
    }

    public function setFromEmail(?array $fromEmail): self
    {
        $this->fromEmail = $fromEmail;

        return $this;
    }

    public function getToEmail(): ?array
    {
        return $this->toEmail;
    }

    public function setToEmail(array $toEmail): self
    {
        $this->toEmail = $toEmail;

        return $this;
    }

    public function getCcEmail(): ?array
    {
        return $this->ccEmail;
    }

    public function setCcEmail(?array $ccEmail): self
    {
        $this->ccEmail = $ccEmail;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getEmailType(): ?string
    {
        return $this->emailType;
    }

    public function setEmailType(?string $emailType): self
    {
        $this->emailType = $emailType;

        return $this;
    }
}
