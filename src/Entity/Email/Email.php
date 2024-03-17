<?php

namespace App\Entity\Email;

use App\Repository\EmailRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=EmailRepository::class)
 * @ApiResource(
 *     attributes={"access_control"="is_granted('ROLE_ADMIN')"},
 *     collectionOperations={
 *         "get"={},
 *     }
 * )
 */
class Email
{
    public const EMAIL_TYPES = [
        'smtp_hostinger',
        'smtp_gmail',
        'templated_email_gmail'
    ];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * ['email' => 'exemple@gmail.com', 'name' => 'Vincent']
     * @ORM\Column(type="array")
     * 
     * @Assert\NotBlank(allowNull=false)
     */
    private $fromEmail = [];

    /**
     * ['email' => 'exemple@gmail.com', 'name' => 'Vincent']
     * @ORM\Column(type="array")
     * 
     * @Assert\NotBlank(allowNull=false)
     */
    private $toEmail = [];

    /**
     * [
     *  ['email' => 'exemple1@gmail.com', 'name' => 'Julien'],
     *  ['email' => 'exemple2@gmail.com', 'name' => 'Vincent']
     * ]
     * @ORM\Column(type="array", nullable=true)
     */
    private $ccEmail = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(allowNull=false)
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank(allowNull=false)
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(allowNull=false)
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

    public function setFromEmail(array $fromEmail): self
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
