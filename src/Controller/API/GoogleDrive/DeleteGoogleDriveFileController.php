<?php

namespace App\Controller\API\GoogleDrive;

use App\Service\GoogleDriveService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DeleteGoogleDriveFileController extends AbstractController
{
    private GoogleDriveService $gDriveService;

    public function __construct(
        GoogleDriveService $gDriveService
    )
    {
        $this->gDriveService = $gDriveService;
    }

    public function __invoke(Request $request): Response
    {
        $content = json_decode($request->getContent(), true);
        if (empty($content['fileId'])) {
            throw new BadRequestException('fileId is required');
        }

        $this->gDriveService->deleteFileOrFolder($content['fileId']);

        return $this->json(null, Response::HTTP_NO_CONTENT); // 204 et corps vide
    }
}