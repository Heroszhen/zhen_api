<?php

namespace App\Controller\API\GoogleDrive;

use App\Service\GoogleDriveService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Dto\GoogleDriveFileAddFolderDto;
use App\Entity\GoogleDriveFile;
use Google\Service\Drive\DriveFile;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AddGoogleDriveFolderController extends AbstractController
{
    private GoogleDriveService $gDriveService;
    private ValidatorInterface $validator;

    public function __construct(
        GoogleDriveService $gDriveService,
        ValidatorInterface $validator
    )
    {
        $this->gDriveService = $gDriveService;
        $this->validator = $validator;
    }

    public function __invoke(GoogleDriveFileAddFolderDto $data): GoogleDriveFile
    {
        $errors = $this->validator->validate($data);
        if (count($errors) > 0) {
            /** @var ConstraintViolation $error */
            foreach ($errors as $error) {
                throw new BadRequestException("{$error->getPropertyPath()}: {$error->getMessage()}");
            }
        }

        if ($this->gDriveService->hasElement($data->name, $data->parent)) {
            throw new BadRequestException("{$data->name} exists");
        }

        $folder = $this->gDriveService->addFolder($data->name, $data->parent);
        if (!$folder instanceof DriveFile) {
            throw new \Exception('Error');
        }

        return $this->gDriveService->setGoogleDriveFile($folder);
    }
}