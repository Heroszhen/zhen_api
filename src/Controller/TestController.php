<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/test")
 */
class TestController extends AbstractController
{
    /**
     * @Route("/test", name="app_test")
     */
    public function index(): Response
    {
        return $this->render('email/index.html.twig', [
            'content' => 'abd',
        ]);
    }
}
