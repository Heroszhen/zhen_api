<?php

namespace App\Entity;

use App\Repository\S3FileRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Controller\API\AddS3FileJSController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=S3FileRepository::class)
 * 
 * @ApiResource(
 *      collectionOperations={
 *         "get"={
 *              "normalization_context"={"group"={"read"}}
 *          },
 *          "post_js" = {
 *              "access_control"="is_granted('ROLE_ADMIN')",
 *              "method" = "POST",
 *              "path" = "/s3file/file",
 *              "controller" = AddS3FileJSController::class,
 *              "openapi_context"={"summary"="add file with FormDate in js"},
 *              "deserialize"=false,
 *              "openapi_context"={
 *                 "requestBody"={
 *                     "content"={
 *                         "multipart/form-data"={
 *                             "schema"={
 *                                 "type"="object",
 *                                 "required" = {"file"},
 *                                 "properties"={
 *                                     "file"={
 *                                         "type"="string",
 *                                         "format"="binary",
 *                                         "required"=true
 *                                     },
 *                                     "bucket"={
 *                                         "type"="string",
 *                                          "format"="string"
 *                                     },
 *                                     "path"={
 *                                         "type"="string",
 *                                          "format"="string"
 *                                     },
 *                                     "new_name"={
 *                                         "type"="string",
 *                                          "format"="string"
 *                                     },
 *                                 }
 *                             }
 *                         }
 *                     }
 *                 }
 *             }
 *          }
 *      },
 *      itemOperations={
 *         "get"={
 *              "normalization_context"={"group"={"read"}}
 *          }
 *      }
 * )
 */
class S3File
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var File|null
     * @Assert\File(
     *     maxSize = "15M",
     *     maxSizeMessage = "15M at most => {{ size }}M "
     * )
     */
    private $file = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(allowNull=false)
     */
    private $bucket;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $path = '';

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $newName;

    // --------------------------file info from aws s3-------------------------
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $fullName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $extension;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $size;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $updated;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getBucket(): ?string
    {
        return $this->bucket;
    }

    public function setBucket(?string $bucket): self
    {
        $this->bucket = $bucket;

        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getNewName(): ?string
    {
        return $this->newName;
    }

    public function setNewName(?string $newName): self
    {
        $this->newName = $newName;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(?string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(?int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(?string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getUpdated(): ?string
    {
        return $this->updated;
    }

    public function setUpdated(?string $updated): self
    {
        $this->updated = $updated;

        return $this;
    }
}
