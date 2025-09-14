<?php

namespace App\Controller\API\S3File;

use App\Entity\S3File;
use App\Service\S3Service;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class GetS3FileUrlController extends AbstractController
{
    private $validator;
    private $s3Service;

    public function __construct(
        ValidatorInterface $validator, 
        S3Service $s3Service
    )
    {
        $this->validator = $validator;
        $this->s3Service = $s3Service;
    }

    /**
     * @return S3File|JsonResponse 
     */
    public function __invoke(Request $request)
    {
        $info = $this->s3Service->getHydraMetadata();
        
        $content = json_decode($request->getContent(), true);
        
        $s3file = new S3File();
        $s3file
            ->setBucket($content['bucket'])
            ->setPath($content['path'])
        ;

        $errors = $this->validator->validate($s3file, null, ['check_path']);
        if (0 !== $errors->count()) {
            return $s3file;
        } 

        $info['url'] =  $this->s3Service->getFileUrl($content['bucket'], $content['path']);
        
        return $this->json($info, Response::HTTP_OK);
    }
}
