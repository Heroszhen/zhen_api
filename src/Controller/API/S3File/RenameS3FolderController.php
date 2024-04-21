<?php

namespace App\Controller\API\S3File;

use App\Entity\S3File;
use App\Exception\AWS\ElementExistingException;
use App\Service\S3Service;
use App\Service\UtilService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Aws\Result;
use Symfony\Component\HttpFoundation\Response;

final class RenameS3FolderController extends AbstractController
{
    private $validator;
    private $s3Service;
    private $utilService;

    public function __construct(
        ValidatorInterface $validator, 
        S3Service $s3Service,
        UtilService $utilService
    )
    {
        $this->validator = $validator;
        $this->s3Service = $s3Service;
        $this->utilService = $utilService;
    }

    /**
     * @return S3File|JsonResponse 
     */
    public function __invoke(Request $request)
    {
        $info = [
            "@context" => "/api/contexts/S3File",
            "@type" => "S3File",
            "@id" => "/api/s3files/rename_folder",
            "hydra:member" => []
        ];
        
        $content = json_decode($request->getContent(), true);
        $s3file = new S3File();
        $s3file
            ->setBucket($content['bucket'])
            ->setPath($content['path'])
            ->setNewName($content['newName'])
        ;

        if (
            !$this->utilService->strEndsWith($content['path'], '/') ||
            !$this->utilService->strEndsWith($content['newName'], '/')
        ) {
            throw new BadRequestHttpException('This is not a folder');
        }

        if ($content['path'] === $content['newName']) {
            throw new BadRequestHttpException('Two paths are same');
        }

        $errors = $this->validator->validate($s3file, null, ['check_path', 'check_newname']);
        if (0 !== $errors->count()) {
            return $s3file;
        } 
        
        $result = $this->s3Service->hasElement($content['bucket'], $content['path']);
        if (!$result) {
            throw new ElementExistingException('This element is not existing');
        }
    
        $this->s3Service->copyFolder($content['bucket'], $content['path'], $content['newName']);
        $this->s3Service->deleteFolder($content['bucket'], $content['path']);
        $info['hydra:member'] = $this->s3Service->listFolder($content['bucket'], $content['newName']);
    
        return $this->json($info, Response::HTTP_CREATED);
    }
}
