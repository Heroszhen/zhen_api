<?php

namespace App\Controller\API\S3File;

use App\Dto\BucketDto;
use App\Entity\S3File;
use App\Service\S3Service;
use App\Service\UtilService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class GetS3BucketController extends AbstractController
{
    private $s3Service;
    private $utilService;
    private $validator;

    public function __construct(
        S3Service $s3Service,
        UtilService $utilService,
        ValidatorInterface $validator
    )
    {
        $this->s3Service = $s3Service;
        $this->utilService = $utilService;
        $this->validator = $validator;
    }

    public function __invoke(Request $request): JsonResponse
    {
        $content = json_decode($request->getContent(), true);

        if('' !== $content['path'] && !$this->utilService->strEndsWith($content['path'], '/')) {
            $content['path'] .= '/';
        }

        $s3file = new S3File();
        $s3file
            ->setBucket($content['bucket'])
            ->setPath($content['path'])
        ;

        $errors = $this->validator->validate($s3file, null, ['check_path']);
        if (0 !== $errors->count()) {
            throw new BadRequestHttpException((string)$errors);
        } 

        if ('/' !== $s3file->getPath()) {
            $result = $this->s3Service->hasElement($s3file->getBucket(), $s3file->getPath());
            if (!$result) {
                throw new BadRequestHttpException('This element does not exist');
            }
        }

        $s3file->setPath('/' === $content['path'] ? '' : $content['path']);
        /**@var BucketDto $bucketDto */
        $bucketDto = $this->s3Service->getBucketByFolder($s3file->getBucket(), $s3file->getPath());
        
        return $this->json($bucketDto);
    }
}
