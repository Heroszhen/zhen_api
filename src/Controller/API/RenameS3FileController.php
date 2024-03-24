<?php

namespace App\Controller\API;

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

final class RenameS3FileController extends AbstractController
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
            "@id" => "/api/s3files/rename_file",
        ];
        
        $content = json_decode($request->getContent(), true);
        $s3file = new S3File();
        $s3file
            ->setBucket($content['bucket'])
            ->setPath($content['path'])
            ->setNewName($content['newName'])
        ;

        if (
            $this->utilService->strEndsWith($content['path'], '/') ||
            $this->utilService->strEndsWith($content['newName'], '/')
        ) {
            throw new BadRequestHttpException('This is not a file');
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

        $result = $this->s3Service->hasElement($content['bucket'], $content['newName']);
        if ($result) {
            throw new ElementExistingException('The new path is existing');
        }

        /** @var Result $result */
        $result = $this->s3Service->copyFile($content['bucket'], $content['path'], $content['newName']);
        if (isset($result->get('CopyObjectResult')['ETag'])) {
            $this->s3Service->deleteFile($content['bucket'], $content['path']);

            $tab = $this->s3Service->getFileInfo($content['bucket'], $content['newName']);
            $info = array_merge($info, $tab);
        } else {
            throw new BadRequestHttpException('There is an error');
        }
    
        return $this->json($info);
    }
}
