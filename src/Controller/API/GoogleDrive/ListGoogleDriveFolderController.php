<?php

namespace App\Controller\API\GoogleDrive;

use App\Entity\GoogleDriveFile;
use App\Service\GoogleDriveService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ListGoogleDriveFolderController extends AbstractController
{
    private GoogleDriveService $gDriveService;

    public function __construct(
        GoogleDriveService $gDriveService
    )
    {
        $this->gDriveService = $gDriveService;
    }

    public function __invoke(GoogleDriveFile $data): array
    {
        if (is_null($data->getFileId())) {
            throw new BadRequestException('fileId is require');
        } 
        
        return $this->gDriveService->listFolder($data->getFileId());
    }
}
