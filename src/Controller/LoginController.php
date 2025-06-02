<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;

class LoginController extends AbstractController
{
    #[Route(path: '/connexion', name: 'app_login')]
public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
{
    
   // Erreurs éventuelles
    $error = $authenticationUtils->getLastAuthenticationError();
    $lastUsername = $authenticationUtils->getLastUsername();

    return $this->render('security/login.html.twig', [
        'last_username' => $lastUsername,
        'error' => $error
    ]);
}

    // la route de déconnexion // 

    #[Route(path: '/deconnexion', name: 'app_logout')]
    public function logout(): void
    {
        
    }
}
