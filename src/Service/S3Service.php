<?php

namespace App\Service;

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
                'key' => $_ENV['ACCESS_KEY_ID'],
                'secret'  => $_ENV['SECRET_ACCESS_KEY'],
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
            }
            $elm['updated'] = $object['LastModified']->format('Y-m-d h:i:s');

            $tab[$key] = $elm;
        }

        return $tab;
    }

    public function isChild(string $path, string $filePath): bool
    {
        $rest = str_replace($path, '', $filePath, $count);
        if ($this->utilService->strEndsWith($rest, '/')) {
            $tab = explode('/', $rest);
            if (count($tab) <= 2) {
                return true;
            }
        } else {
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
}