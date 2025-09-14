<?php

namespace App\Controller\API\S3File;

use App\Entity\S3File;
use App\Service\S3Service;
use App\Service\UtilService;
use Aws\Result;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class AddS3FolderController extends AbstractController
{
    private ValidatorInterface $validator;
    private S3Service $s3Service;
    private UtilService $utilService;
    private NormalizerInterface $normalizer;

    public function __construct(
        ValidatorInterface $validator, 
        S3Service $s3Service,
        UtilService $utilService,
        NormalizerInterface $normalizer
    )
    {
        $this->validator = $validator;
        $this->s3Service = $s3Service;
        $this->utilService = $utilService;
        $this->normalizer = $normalizer;
    }

    /**
     * @return S3File|JsonResponse 
     */
    public function __invoke(Request $request)
    {
        $info = $this->s3Service->getHydraMetadata();
      
        $content = json_decode($request->getContent(), true);
        if(!$this->utilService->strEndsWith($content['path'], '/')) {
            $content['path'] .= '/';
        }
        $s3file = new S3File();
        $s3file
            ->setBucket($content['bucket'])
            ->setPath($content['path'])
        ;

        $errors = $this->validator->validate($s3file);
        if (0 !== $errors->count()) {
            throw new BadRequestHttpException((string)$errors);
        } 

        $result = $this->s3Service->hasElement($content['bucket'], $content['path']);
        if ($result) {
            throw new BadRequestHttpException('The folder exists');
        }
        
        $result = $this->s3Service->addOneFile($content['bucket'], $content['path']);
        if (!$result instanceof Result) {
            throw new Exception('Error');
        }

        $s3file
            ->setId(1)
            ->setName($this->s3Service->getNameFromPath($content['path']))
            ->setFullName($content['path'])
        ;
        $data = $this->normalizer->normalize($s3file, null, ['groups' => ['read']]);  

        return $this->json(array_merge($this->s3Service->getHydraMetadata(), $data), Response::HTTP_CREATED);
    }
}
