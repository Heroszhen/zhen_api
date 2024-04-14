<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Traits\DateTrait;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 * @UniqueEntity(fields={"email"}, message="Il existe déjà un utilisateur avec ce mail")
 * 
 * @ApiResource(
 *      attributes={
 *          "access_control"="is_granted('ROLE_ADMIN')",
 *          "normalization_context"={"groups"={"read"}},
 *          "denormalization_context"={"groups"={"write"}}
 *      },
 *      collectionOperations={
 *         "get"={},
 *         "post"={}
 *      },
 *      itemOperations={
 *         "get"={},
 *         "patch"={},
 *         "patch_update_password"={
 *              "method"="PATCH",
 *              "path"="/users/{id}/password",
 *              "controller"=App\Controller\API\User\UpdateUserPasswordController::class,
 *              "denormalization_context"={"groups"={"pwd:input"}},
 *         }
 *      }
 * )
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @Groups({"read"})
     */
    use DateTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"read", "write"})
     * @Assert\NotBlank
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     * @Groups({"read", "write"})
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * 
     * @Assert\NotBlank
     * @ORM\Column(type="string")
     * @Groups({"write", "pwd:input"})
     */
    private $password = 'INIT';

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $apiKey;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    public function setApiKey(?string $apiKey): self
    {
        $this->apiKey = $apiKey;

        return $this;
    }
}
