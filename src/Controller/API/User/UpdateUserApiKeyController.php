<?php

namespace App\Controller\API\User;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

final class UpdateUserApiKeyController extends AbstractController
{
    public function __invoke(User $data, Request $request): User
    {
        $content = json_decode($request->getContent(), true);
        $newApikey = '';
        if (isset($content['apiKey'])) {
            $newApikey = $content['apiKey'];
        }
        $newApikey = bin2hex(openssl_random_pseudo_bytes(16) . $newApikey);
        $data->setApiKey($newApikey);

        return $data;
    }
}