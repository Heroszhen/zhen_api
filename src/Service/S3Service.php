<?php

namespace App\Service;

use App\Dto\BucketDto;
use Aws\Result;
use Aws\S3\S3Client;
use App\Service\UtilService;
use Aws\S3\Exception\S3Exception;

class S3Service
{
    private S3Client $s3Client;
    private UtilService $utilService;

    public function __construct(UtilService $utilService)
    {
        $this->s3Client = new S3Client([
            'region' => 'eu-west-3',
            'version' => 'latest',
            'credentials' => array(
                'key' => $_ENV['AWS_ACCESS_KEY_ID'],
                'secret'  => $_ENV['AWS_SECRET_ACCESS_KEY'],
            )
        ]);

        $this->utilService = $utilService;
    }

    public function getClient(): S3Client
    {
        return $this->s3Client;
    }

    /**
     * @exemple listFolder("zhen_test", "movie/")
     * 
     * @return array
     */
    public function listFolder(string $bucket, string $path, bool $level1 = false): array
    {
        $tab = [];

        $objects = $this->s3Client->getIterator('ListObjects', [
            "Bucket" => $bucket,
            "Prefix" => $path,
            'Delimiter' => ""
        ]);
        foreach ($objects as $key => $object) {
            if ($level1 && !$this->isChild($path, $object['Key'])) {
                continue;
            }

            $elm = [];
            $elm['name'] = $this->getNameFromPath($object['Key']);
            $elm['fullName'] = $object['Key'];
            $elm['size'] = $object['Size'];
            $elm['extension'] = null;
            if (!$this->utilService->strEndsWith($object['Key'], '/')) {
                $array = explode('.', $object['Key']);
                $elm['extension'] = end($array);
                $elm['url'] = $this->getFileUrl($bucket, $object['Key']);
            } else {
                if ($object['Key'] === $path) {
                    continue;
                }
            }
            $elm['updated'] = $object['LastModified']->format('Y-m-d h:i:s');

            $tab[] = $elm;
        }

        return $tab;
    }

    public function isChild(string $path, string $filePath): bool
    {
        $rest = str_replace($path, '', $filePath, $count);
        if ($this->utilService->strEndsWith($rest, '/')) {//folder
            $tab = explode('/', $rest);
            if (count($tab) <= 2) {
                return true;
            }
        } else {//file
            if (!$this->utilService->strContains($rest, '/')) {
                return true;
            }
        }

        return false;
    }

    public function getNameFromPath($path): string
    {
        if (!$this->utilService->strContains($path, '/')) {
            return $path;
        }

        $tab = explode('/', $path);
        if ('' === end($tab)) {
            array_pop($tab);
        }

        return end($tab);
    }

    public function getFileInfo(string $bucket, string $path): array
    {
        $info = [];
       
        $tab = explode('/', $path);
        array_pop($tab);
        $folderPath = implode('/', $tab) . '/';

        $tab = $this->listFolder($bucket, $path, true);
        foreach($tab as $item) {
            if ($path === $item["fullName"]) {
                $info = $item;
                
                break;
            }
        }
       
       return $info;
    }

    /**
     * add one file or one folder
     * 
     * @exemple addOneFile("zhen_test", "movie/", fileUrl)
     * 
     * @exemple addOneFile("zhen_test", "movie/aventure/")
     */
    public function addOneFile(string $bucket, string $path, $fileUrl = null, ?string $contentType = null): Result
    {
        $info = [
            'Bucket' => $bucket,
            'Key' => $path,
        ];
        if (null !== $fileUrl) {
            $info['SourceFile'] = $fileUrl;
            $info['ContentType'] = $contentType;
        }

        return $this->s3Client->putObject($info);
    }

    /**
     * @exemple
     * hasElement('zhen', 'abc/')
     * 
     * @exemple
     * hasElement('zhen', 'test/video.mp4')
     */
    public function hasElement(string $bucket, string $path): bool
    {
        if ($this->utilService->strEndsWith($path, '/')) {//folder
            $result = $this->s3Client->listObjects([
                'Bucket' => $bucket, 
                'Prefix' => $path,
            ]);
            return is_null($result->get('Contents')) ? false : true;
        }

        return $this->s3Client->doesObjectExist($bucket, $path);
    }

    public function hasBucket(string $bucket): bool
    {
        try {
            $this->s3Client->headBucket(['Bucket' => $bucket]);

            return true;
        } catch(S3Exception $e) {
            return false;
        }
    }

    public function getFileUrl(string $bucket, string $path, int $delay = 5): string
    {
        $cmd = $this->s3Client->getCommand('GetObject', [
            'Bucket' => $bucket,
            'Key' => $path,
            'ACL' => 'public-read',
        ]);
        
        $request = $this->s3Client->createPresignedRequest($cmd, "+{$delay} minutes");
        
        // Get the actual presigned-url
        $presignedUrl = (string)$request->getUri();
        
        return $presignedUrl;
    }

    public function deleteFile(string $bucket, string $path): void
    {
        $this->s3Client->deleteObject([
            'Bucket' => $bucket,
            'Key' => $path
        ]);
    }

    public function deleteFolder(string $bucket, string $path): void
    {
        $tab = $this->listFolder($bucket, $path);
        $folders = [];
        foreach($tab as $item) {
            if (is_null($item['extension'])) {
                $folders[] = $item;
            } else {
                $this->deleteFile($bucket, $item["fullName"]);
            }
        }
        
        foreach($folders as $item) {
            $this->deleteFile($bucket, $item["fullName"]);
        }

        $this->deleteFile($bucket, $path);
    }

    public function copyFile(string $bucket, string $oldPath, string $newPath): Result
    {
        return $this->s3Client->copyObject([
            'Bucket' => $bucket,
            'Key' => $newPath,
            'CopySource' => $bucket . '/' . $this->s3Client::encodeKey($oldPath)
        ]);
    }

    public function copyFolder(string $bucket, string $oldPath, string $newPath): void
    {
        $objects = $this->s3Client->getIterator('ListObjects', [
            "Bucket" => $bucket,
            "Prefix" => $oldPath,
            'Delimiter' => ""
        ]);
        
        $oldPathTab = explode('/', $oldPath);
        array_pop($oldPathTab);
        foreach ($objects as $object) {
            $elmPath = $object['Key'];
            $isFile = !$this->utilService->strEndsWith($elmPath, '/');
            $paths = explode('/', $elmPath);
            if (!$isFile) {
                array_pop($paths);
            }
            //remove old prefix
            for($i = 0; $i < count($oldPathTab); $i++) {
                array_shift($paths);
            }
            $newElmPath = implode('/', $paths);
            if (!$isFile) {
                $newElmPath .= '/';
            }
            $newElmPath = $newPath . $newElmPath;
            if ($isFile) {
                if ($this->hasElement($bucket, $newElmPath)) {
                    $paths[count($paths) -1] = $this->utilService->getUniqid() . '_' . $paths[count($paths) -1];
                    $newElmPath = implode('/', $paths);
                    $newElmPath = $newPath . $newElmPath;
                }
                $this->copyFile($bucket, $elmPath, $newElmPath);
            } else {
                $this->addOneFile($bucket, $newElmPath);
            }
        }
    }

    public function getHydraMetadata(): array
    {
        return [
            "@context" => "/api/contexts/S3File",
            "@type" => "S3File",
            "@id" => "/api/s3files/folder",
        ];
    }

    public function checkPathsInBucket(string $bucket, string $path): void
    {
        $tab = explode('/', $path);
        $str = '';
        foreach ($tab as $folder) {
            if ($folder === '') {
                return;
            }

            $str .= $folder.'/';
            if (!$this->hasElement($bucket, $str)) {
                $this->addOneFile($bucket, $str);
            }
        }
    }

    public function getBucketByFolder(string $bucket, string $path): BucketDto
    {
        $bucketDto = new BucketDto();
        $bucketDto->bucket = $bucket;
        $bucketDto->path = $path;

        /** @var Result $result */
        $result = $this->s3Client->listObjectsV2([
            "Bucket" => $bucket,
            "Prefix" => $path,
            'Delimiter' => ""
        ]);
        foreach ($result->toArray()['Contents'] as $key => $elm) {
            $bucketDto->size += (int)$elm['Size'];

            if ($this->utilService->strEndsWith($elm['Key'], '/')) {
                $bucketDto->nbFolders++;
            } else {
                $bucketDto->nbFiles++;

                if ($this->utilService->strEndsWith($elm['Key'], '.pdf')) {
                    $bucketDto->nbPDFs++;
                }
            }
        }

        return $bucketDto;
    }
}
