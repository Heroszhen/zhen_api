<?php

namespace App\Controller\API\S3File;

use App\Entity\S3File;
use App\Exception\AWS\ElementExistingException;
use App\Service\S3Service;
use App\Service\UtilService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ListS3FolderController extends AbstractController
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
            "@id" => "/api/s3files/list_folder",
            "hydra:member" => []
        ];
        
        $content = json_decode($request->getContent(), true);
        if('' !== $content['path'] && !$this->utilService->strEndsWith($content['path'], '/')) {
            $content['path'] .= '/';
        }
        $s3file = new S3File();
        $s3file
            ->setBucket($content['bucket'])
            ->setPath($content['path'])
        ;

        $errors = $this->validator->validate($s3file);
        if (0 !== $errors->count()) {
            return $s3file;
        } 
        
        if ('' !== $content['path']) {
            $result = $this->s3Service->hasElement($content['bucket'], $content['path']);
            if (!$result) {
                throw new ElementExistingException('The folder is not existing');
            }
        }

        $info['hydra:member'] = $this->s3Service->listFolder($content['bucket'], $content['path'], true);
        
        return $this->json($info, Response::HTTP_OK);
    }
}
