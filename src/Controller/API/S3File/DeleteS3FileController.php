<?php

namespace App\Controller\API\S3File;

use App\Entity\S3File;
use App\Service\S3Service;
use App\Service\UtilService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class DeleteS3FileController extends AbstractController
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
        $content = json_decode($request->getContent(), true);

        $s3file = new S3File();
        $s3file
            ->setBucket($content['bucket'])
            ->setPath($content['path'])
        ;

        $errors = $this->validator->validate($s3file, null, ['check_path']);
        if (0 !== $errors->count()) {
            throw new BadRequestHttpException((string)$errors);
        } 
        
        $result = $this->s3Service->hasElement($content['bucket'], $content['path']);
        if (!$result) {
            throw new BadRequestHttpException('This element does not exist');
        }

        if ($this->utilService->strEndsWith($content['path'], '/')) {
            $this->s3Service->deleteFolder($content['bucket'], $content['path']);
        } else {
            $this->s3Service->deleteFile($content['bucket'], $content['path']);
        }
    
        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
