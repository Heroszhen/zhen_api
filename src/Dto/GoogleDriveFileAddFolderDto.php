<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class GoogleDriveFileAddFolderDto
{   
    /**
     * @Assert\NotBlank(allowNull=false)
     */
    public string $parent;

    /**
     * @Assert\NotBlank(allowNull=false)
     */
    public string $name;
}