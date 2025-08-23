<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;

/**
 * @Route("/test")
 */
class TestController extends AbstractController
{
    private HttpClientInterface $httpClient;
    private LoggerInterface $logger;

    public function __construct(
        HttpClientInterface $httpClient,
        LoggerInterface $logger
    )
    {
        if ('prod' === $_ENV['APP_ENV']) {
            throw $this->createAccessDeniedException('Forbidden.');
        }

        $this->httpClient = $httpClient;
        $this->logger = $logger;
    }
    
    /**
     * @Route("/test", name="app_test")
     */
    public function index(): Response
    {
        return $this->render('email/index.html.twig', [
            'content' => 'abd',
        ]);
    }

    /**
     * @Route("/upload-file", name="app_test_upload_file")
     */
    public function testFile(): Response
    {
        return $this->render('test/index.html.twig');
    }

    /**
     * @Route("/send-file", name="app_test_send_file", methods={"POST"})
     */
    public function sendFile(Request $request): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->json(null, 404);
        }

        /** @var UploadedFile $file */
        $file = $request->files->get('file');
        $filename = $request->request->get('filename'); 

        $fields = $request->request->all();
        $filePart = DataPart::fromPath(
            $file->getRealPath(),
            $file->getClientOriginalName(), 
            $file->getMimeType() ?: 'application/octet-stream'
        );
        $formParts = array_merge($fields, [
            'file' => $filePart,
            'bucket' => 'zhentest3',
            'path' => 'test5/',
            'newName' => $fields['filename']
        ]);

        $formData = new FormDataPart($formParts);
        $url = "http://127.0.0.1:8001/api/s3files/file";
        $headers = array_merge(
            $formData->getPreparedHeaders()->toArray(),
            [ 'X-AUTH-API-KEY' => $_ENV['AUTH_API_KEY'] ]
        );
        $response = $this->httpClient->request('POST', $url, [
            'headers' => $headers,  
            'body' => $formData->bodyToIterable(),
        ]);
        $status = $response->getStatusCode();
        $content = $response->getContent(false);

        return $this->json([$_ENV['AUTH_API_KEY'], $status, $content], 200);
    }
}
