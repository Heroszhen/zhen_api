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
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class RenameS3FolderController extends AbstractController
{
    private $validator;
    private $s3Service;
    private $utilService;
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
            throw new BadRequestHttpException('This element does not exist');
        }

        $this->s3Service->copyFolder($content['bucket'], $content['path'], $content['newName']);
        $this->s3Service->deleteFolder($content['bucket'], $content['path']);
        
        $s3file
            ->setId(1)
            ->setName($this->s3Service->getNameFromPath($content['newName']))
            ->setFullName($content['newName'])
            ->setSize(0)
            ->setUpdated((new \DateTime())->format('Y-m-d h:s:i'));
        ;
        $data = $this->normalizer->normalize($s3file, null, ['groups' => ['read']]);
    
        return $this->json(array_merge($this->s3Service->getHydraMetadata(), $data), Response::HTTP_CREATED);
    }
}
