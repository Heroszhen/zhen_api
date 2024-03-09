<?php

namespace App\Controller\API;

use App\Entity\S3File;
use App\Service\S3Service;
use App\Service\UtilService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class AddS3FileJSController extends AbstractController
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
        $form = $request->request;
        $file = $request->files->get('file');
        //dd($file->getClientOriginalName(), $file->guessExtension()); 
        
        if (null === $file) {
            throw new BadRequestHttpException('"file" is required');
        }
        
        $s3file = new S3File();
        /** @var File $file */
        $s3file
            ->setFile($file)
            ->setBucket($form->get("bucket"))
            ->setPath($form->get("path"))
            ->setNewName($form->get("new_name"))
        ;

        $errors = $this->validator->validate($s3file);
        if (0 !== $errors->count()) {
            return $s3file;
        } 

        /** @var UploadedFile $file */
        $oldName = $file->getClientOriginalName();
        $tab = explode('.', $oldName);
        $extension = end($tab);
        $newName = (null === $form->get("new_name") || '' === $form->get("new_name")) ? $tab[0] : $form->get("new_name");
        $newName = "{$this->utilService->getUniqid()}_{$newName}.{$extension}";
        $fileUrl = $file->getPathName();
        $type = mime_content_type($fileUrl);
        $this->s3Service->addOneFile($s3file->getBucket(), $s3file->getPath() . $newName, $fileUrl, $type);

        $tab = $this->s3Service->listFolder($s3file->getBucket(), $s3file->getPath(), true);
        $info = [];
        foreach($tab as $elm) {
            if ($elm['name'] === $newName) {
                $info = $elm;
                break;
            }
        }
        $info = array_merge(
            [
            "@context" => "/api/contexts/S3File",
            "@type" => "S3File",
            ],
            $info
        );

        return $this->json($info);
    }
}
