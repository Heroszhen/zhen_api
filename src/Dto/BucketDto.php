<?php

namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Groups;

class BucketDto
{
    /**
     * @Groups({"read"})
     */
    public string $bucket;

    /**
     * @Groups({"read"})
     */
    public string $path;

    /**
     * @Groups({"read"})
     */
    public int $size = 0;

    /**
     * @Groups({"read"})
     */
    public int $nbFolders = 0;

    /**
     * @Groups({"read"})
     */
    public int $nbFiles = 0;
    
    /**
     * @Groups({"read"})
     */
    public int $nbPDFs = 0;
    
    /**
     * @Groups({"read"})
     */
    public \DateTime $created;

    public function __construct()
    {
        $this->created = new \DateTime();
    }
}