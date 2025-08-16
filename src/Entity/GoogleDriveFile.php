<?php

namespace App\Entity;

use App\Repository\GoogleDriveFileRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\API\GoogleDrive\ListGoogleDriveFolderController;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Dto\GoogleDriveFileAddFolderDto;
use App\Controller\API\GoogleDrive\AddGoogleDriveFolderController;
use App\Controller\API\GoogleDrive\DeleteGoogleDriveFolderController;

/**
 * @ORM\Entity(repositoryClass=GoogleDriveFileRepository::class)
 * @ApiResource(
 *      normalizationContext={"groups"={"read"}},
 *      attributes={"access_control"="is_granted('ROLE_ADMIN')"},
 *      collectionOperations={
 *          "get"={},
 *          "post_list_folder"={
 *              "method"="POST",
 *              "path"="/googledrivefiles/list-folder",
 *              "read"=false,
 *              "denormalization_context"={"groups"={"folder:i"}},
 *              "controller"=ListGoogleDriveFolderController::class,
 *              "openapi_context"={
 *                   "summary"="list one folder",
 *              },
 *          },
 *          "post_add_folder"={
 *              "method"="POST",
 *              "path"="/googledrivefiles/folder",
 *              "controller"=AddGoogleDriveFolderController::class,
 *              "input"=GoogleDriveFileAddFolderDto::class,
 *              "openapi_context"={
 *                   "summary"="add one folder",
 *              },
 *          },
 *          "post_delete_file"={
 *              "method"="POST",
 *              "path"="/googledrivefiles/delete",
 *              "controller"=DeleteGoogleDriveFolderController::class,
 *              "openapi_context"={
 *                   "summary"="delete one file or one folder",
 *              },
 *         },
 *      },
 *      itemOperations={
 *         "get"={
 *              "normalization_context"={"group"={"read"}}
 *          },
 *          "delete"={}
 *      }
 * )
 */
class GoogleDriveFile extends S3File
{
    /**
     * @Groups({"folder:i", "read"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fileId;

    /**
     * @Groups({"read"})
     * @ORM\Column(type="json")
     */
    private $parents = [];

    public function getFileId(): ?string
    {
        return $this->fileId;
    }

    public function setFileId(?string $fileId): self
    {
        $this->fileId = $fileId;

        return $this;
    }

    public function getParents(): ?array
    {
        return $this->parents;
    }

    public function setParents(array $parents): self
    {
        $this->parents = $parents;

        return $this;
    }
}
