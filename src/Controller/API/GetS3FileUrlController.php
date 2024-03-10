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

final class GetS3FileUrlController extends AbstractController
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
            "@id" => "/api/s3file/file_url",
        ];
        
        $content = json_decode($request->getContent(), true);
        if (null === $content['path'] || '' === $content['path']) {
            throw new BadRequestHttpException('"path" is required');
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
        
        $result = $this->s3Service->hasElement($content['bucket'], $content['path']);
        if (!$result) {
            throw new ElementExistingException('The file is not existing');
        }

        $info['url'] =  $this->s3Service->getFileUrl($content['bucket'], $content['path']);
        
        return $this->json($info);
    }
}
