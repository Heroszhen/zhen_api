<?php

namespace App\Controller\API;

use App\Entity\S3File;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class AddS3FileJSController extends AbstractController
{
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @return S3File|JsonResponse 
     */
    public function __invoke(Request $request)
    {
        $form = $request->request;
        $file = $request->files->get('file');
        //dd($file->getClientOriginalName(), $file->guessExtension());       

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

        return $this->json([
            "@context" => "\/api\/contexts\/S3File",
            "@type" => "S3File",
            "s3file" => $file->getSize(),
            "old_name" => "",
            "new_name" => "",
        ]);
    }
}
