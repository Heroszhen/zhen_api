<?php

namespace App\Controller;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/", name="app_security_login", methods={"GET", "POST"})
     */
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        if (null !== $this->getUser()) {
            return $this->redirectToRoute('admin_user_list');
        }

        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/index.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    /**
     * @Route("/logout", name="app_security_logout", methods={"GET"})
     */
    public function logout(): void
    {}

    /**
     * @Route("/get-login-token", name="app_security_token", methods={"GET"})
     */
    public function getLoginToken(Request $request, JWTTokenManagerInterface $JWTManager): ?JsonResponse
    {   
        if ($request->isXmlHttpRequest()) {
            return $this->json(['data' => $JWTManager->create($this->getUser())]);
        }

        return null;
    }
}
