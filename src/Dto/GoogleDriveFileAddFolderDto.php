<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class GoogleDriveFileAddFolderDto
{   
    /**
     * @Assert\NotBlank(allowNull=false)
     */
    public string $parents;

    /**
     * @Assert\NotBlank(allowNull=false)
     */
    public string $name;
}