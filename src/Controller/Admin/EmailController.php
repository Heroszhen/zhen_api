<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmailController extends AbstractController
{
    /**
     * @Route("/admin/email", name="app_admin_email")
     */
    public function index(): Response
    {
        return $this->render('admin/email/index.html.twig', [
            'controller_name' => 'EmailController',
        ]);
    }
}
