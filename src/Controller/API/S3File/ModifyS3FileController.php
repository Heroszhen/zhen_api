<?php

namespace App\Controller\API\S3File;

use App\Entity\S3File;
use App\Service\S3Service;
use App\Service\UtilService;
use Aws\Result;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ModifyS3FileController extends AbstractController
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
            throw new BadRequestHttpException('Bad requests');
        } 

        if ($this->utilService->strEndsWith($s3file->getPath(), '/')) {
            throw new BadRequestHttpException('This is not a file');
        }

        if (!isset($content['content'])) {
            throw new BadRequestHttpException('Content is required');
        }

        $info = $this->s3Service->getFileInfo($s3file->getBucket(), $s3file->getPath());
        if (empty($info)) {
            throw new BadRequestHttpException('File is not existed');
        }

        $head = $this->s3Service->getHead($s3file->getBucket(), $s3file->getPath());
        if (!$head instanceof Result) {
            throw new BadRequestHttpException('Error');
        }

        if (!$this->utilService->strContains($head->get('ContentType'), 'text/plain')) {
            throw new BadRequestHttpException('File extension is not authorized');
        }

        $this->s3Service->modifyOneFile(
            $s3file->getBucket(), 
            $s3file->getPath(),
            $content['content'],
            $head->get('ContentType')
        );

        return $this->json(null, Response::HTTP_OK);
    }
}
