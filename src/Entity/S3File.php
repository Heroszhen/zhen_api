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
 *              "path" = "/s3file/js_formdata",
 *              "controller" = AddS3FileJSController::class,
 *              "openapi_context"={"summary"="add file with FormDate in js"},
 *              "deserialize"=false,
 *              "openapi_context"={
 *                 "requestBody"={
 *                     "content"={
 *                         "multipart/form-data"={
 *                             "schema"={
 *                                 "type"="object",
 *                                 "properties"={
 *                                     "file"={
 *                                         "type"="string",
 *                                         "format"="binary"
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
     * @Assert\NotBlank(allowNull=false)
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
     * @Assert\NotBlank(allowNull=false)
     */
    private $path;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $newName;

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

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): self
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
}
