<?php

namespace App\Service;

use App\Dto\GoogleDriveFileAddFolderDto;
use App\Entity\GoogleDriveFile;
use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Service\UtilService;

class GoogleDriveService
{
    private Client $client;
    private Drive $drive;
    private ParameterBagInterface $parameterBag;
    private UtilService $utilService;
    private const FILE_FIELDS = 'id,name,mimeType,parents,modifiedTime,webViewLink,size';

    public function __construct(
        ParameterBagInterface $parameterBag,
        UtilService $utilService
    )
    {
        $this->parameterBag = $parameterBag;
        $this->utilService = $utilService;

        $this->client = new Client();
        $this->client->setApplicationName($_ENV['GOOGLE_SERVICE_ACCOUNT_NAME']);
        $this->client->setAuthConfig($this->parameterBag->get('resources').'/'.$_ENV['GOOGLE_SERVICE_ACCOUNT_CREDENTIALS']);
        $this->client->setScopes([Drive::DRIVE]); 
        $this->drive = new Drive($this->client);
    }

    public function listFolder(string $fileId)
    {
        $res = $this->drive->files->listFiles([
            'q' => sprintf("'%s' in parents and trashed = false", $fileId),
            'fields' => 'files(' .self::FILE_FIELDS. ')',
            'supportsAllDrives' => true,
            'includeItemsFromAllDrives' => true,
        ]);
        
        return array_map(
            fn(DriveFile $file): GoogleDriveFile => $this->setGoogleDriveFile($file),
            $res->getFiles() 
        );
    }

    public function setGoogleDriveFile(DriveFile $driveFile): GoogleDriveFile
    {
        $file = new GoogleDriveFile();
        $tab = explode('.', $driveFile->getName());
        $extension = $this->utilService->strContains($driveFile->getMimeType(), '.folder') ? null : end($tab);
        $file
            ->setFileId($driveFile->getId())
            ->setParents($driveFile->getParents())
            ->setName($driveFile->getName())
            ->setSize($driveFile->getSize())
            ->setExtension($extension)
            ->setUpdated($driveFile->getModifiedTime())
            ->setUrl($driveFile->getWebViewLink())
            ->setBucket("google-drive")
            ->setId(1)
        ;

        return $file;
    }

    public function addFolder(GoogleDriveFileAddFolderDto $data): DriveFile
    {
        $fileMetadata = new DriveFile([
            'name' => $data->name,
            'mimeType' => 'application/vnd.google-apps.folder',
        ]);
        $fileMetadata->setParents([$data->parents]);

        $folder = $this->drive->files->create($fileMetadata, [
            'fields' => self::FILE_FIELDS
        ]);

        return $folder;
    }
}