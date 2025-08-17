<?php

namespace App\Controller\API\GoogleDrive;

use App\Service\GoogleDriveService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\GoogleDriveFile;
use Google\Service\Drive\DriveFile;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;

class AddGoogleDriveFileController extends AbstractController
{
    private GoogleDriveService $gDriveService;

    public function __construct(
        GoogleDriveService $gDriveService
    )
    {
        $this->gDriveService = $gDriveService;
    }

    public function __invoke(Request $request)
    {
        $form = $request->request;
        $file = $request->files->get('file');
        
        if (null === $file) {
            throw new BadRequestHttpException('file is required');
        }

        if (empty($form->get('parent'))) {
            throw new BadRequestHttpException('parent is required');
        }

        /** @var UploadedFile $file */
        $filename = $file->getClientOriginalName();
        if (!empty($form->get('filename'))) {
            $filename = $form->get('filename');   
        }

        if ($this->gDriveService->hasElement($filename, $form->get('parent'))) {
            $filename = uniqid().'_'.$filename;
        }

        $file = $this->gDriveService->addFile($file, $filename, $form->get('parent'));
        
        return $this->gDriveService->setGoogleDriveFile($file);
    }
}