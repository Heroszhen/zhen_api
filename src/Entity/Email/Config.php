<?php

namespace App\Entity\Email;

use App\Repository\Email\ConfigRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ConfigRepository::class)
 * @ORM\Table(name="email_config")
 */
class Config
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $host;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $port;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $emailType;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $fromEmail;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $fromEmailName;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getHost(): ?string
    {
        return $this->host;
    }

    public function setHost(?string $host): self
    {
        $this->host = $host;

        return $this;
    }

    public function getPort(): ?int
    {
        return $this->port;
    }

    public function setPort(?int $port): self
    {
        $this->port = $port;

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

    public function getFromEmail(): ?string
    {
        return $this->fromEmail;
    }

    public function setFromEmail(?string $fromEmail): self
    {
        $this->fromEmail = $fromEmail;

        return $this;
    }

    public function getFromEmailName(): ?string
    {
        return $this->fromEmailName;
    }

    public function setFromEmailName(?string $fromEmailName): self
    {
        $this->fromEmailName = $fromEmailName;

        return $this;
    }
}
