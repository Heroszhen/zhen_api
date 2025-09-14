<?php

namespace App\Entity;

use App\Repository\S3FileRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Controller\API\S3File\AddS3FileJSController;
use App\Controller\API\S3File\AddS3FolderController;
use App\Controller\API\S3File\ListS3FolderController;
use App\Controller\API\S3File\GetS3FileUrlController;
use App\Controller\API\S3File\DeleteS3FileController;
use App\Controller\API\S3File\RenameS3FileController;
use App\Controller\API\S3File\RenameS3FolderController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as AppAssert;

/**
 * @ORM\Entity(repositoryClass=S3FileRepository::class)
 * 
 * @ApiResource(
 *      attributes={
 *          "access_control"="is_granted('ROLE_ADMIN')",
 *          "normalization_context"={"groups"={"read"}},
 *      },
 *      collectionOperations={
 *         "get"={},
 *         "post_rename_folder"={
 *              "method"="POST",
 *              "path"="/s3files/rename-folder",
 *              "controller"=RenameS3FolderController::class,
 *              "denormalization_context"={"groups"={"input"}},
 *              "validation_groups"={"check_path", "check_newname"},
 *              "openapi_context"={
 *                   "summary"="rename one folder",
 *              },
 *         },
 *         "post_rename_file"={
 *              "method"="POST",
 *              "path"="/s3files/rename-file",
 *              "controller"=RenameS3FileController::class,
 *              "denormalization_context"={"groups"={"input"}},
 *              "validation_groups"={"check_path", "check_newname"},
 *              "openapi_context"={
 *                   "summary"="rename one file",
 *              },
 *         },
 *         "post_delete_file"={
 *              "method"="POST",
 *              "path"="/s3files/delete",
 *              "controller"=DeleteS3FileController::class,
 *              "denormalization_context"={"groups"={"input"}},
 *              "validation_groups"={"check_path"},
 *              "openapi_context"={
 *                   "summary"="delete one file or one folder",
 *              },
 *         },
 *         "post_get_file_url"={
 *              "method"="POST",
 *              "path"="/s3files/file-url",
 *              "controller"=GetS3FileUrlController::class,
 *              "denormalization_context"={"groups"={"input"}},
 *              "validation_groups"={"check_path"},
 *              "openapi_context"={
 *                   "summary"="get presigned url of one file",
 *              },
 *         },
 *         "post_list_folder"={
 *              "access_control"="is_granted('ROLE_ADMIN')",
 *              "method"="POST",
 *              "path"="/s3files/list-folder",
 *              "controller"=ListS3FolderController::class,
 *              "denormalization_context"={"groups"={"input"}},
 *              "openapi_context"={
 *                   "summary"="get elements of one folder",
 *              },
 *         },
 *         "post_add_folder"={
 *              "method"="POST",
 *              "path"="/s3files/folder",
 *              "controller"=AddS3FolderController::class,
 *              "denormalization_context"={"groups"={"input"}},
 *              "openapi_context"={
 *                   "summary"="add one folder",
 *              },
 *          },
 *          "post_add_file" = {
 *              "method"="POST",
 *              "path"="/s3files/file",
 *              "controller"=AddS3FileJSController::class,
 *              "deserialize"=false,
 *              "openapi_context"={
 *                 "summary"="add file with FormDate in js",
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
 *          },
 *          "delete"={}
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
     * @AppAssert\CheckS3Bucket
     * @Groups({"input", "read"})
     */
    private $bucket;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"input", "read"})
     * @Assert\NotBlank(allowNull=false, groups={"check_path"})
     */
    private $path = '';

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(allowNull=false, groups={"check_newname"})
     */
    private $newName;

    // --------------------------file info from aws s3-------------------------
    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"read"})
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"read"})
     */
    private $fullName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"read"})
     */
    private $extension = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"read"})
     */
    private $size;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"read"})
     */
    private $updated;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"read"})
     */
    private $url;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;
        
        return $this;
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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }
}
