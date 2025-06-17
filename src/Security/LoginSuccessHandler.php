<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private RouterInterface $router;
    private AuthorizationCheckerInterface $authorizationChecker;

    public function __construct(RouterInterface $router, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->router = $router;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): ?Response
    {
        if ($this->authorizationChecker->isGranted('ROLE_EMPLOYE')) {
            // L'utilisateur est un employé, rediriger vers la liste des avis
            $response = new RedirectResponse($this->router->generate('app_admin_employe_avis_list'));
        } else {
            // Pour les autres utilisateurs, rediriger vers la page de compte par défaut ou la page d'accueil
            // Choisissez la route qui convient le mieux pour les utilisateurs non-employés
            $response = new RedirectResponse($this->router->generate('app_account')); // Ou 'app_home', etc.
        }

        return $response;
    }
}