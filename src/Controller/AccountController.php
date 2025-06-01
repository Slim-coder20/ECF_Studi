<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AccountController extends AbstractController
{   
    // cette route permet d'afficher le compte de l'utilisateur connecté // 
    // elle est accessible via l'URL /compte et utilise le template account.html.twig //
    // elle récupère l'utilisateur connecté via $this->getUser() et le passe au template //
    // pour afficher ses informations dans la vue //
    // elle est protégée par le système de sécurité de Symfony, donc l'utilisateur doit être connecté pour y accéder //
    // si l'utilisateur n'est pas connecté, il sera redirigé vers la page de connexion //
    #[Route('/compte', name: 'app_account')]
    public function index(): Response

    {
        $user = $this->getUser();
        if (!$user) {
            // Si l'utilisateur n'est pas connecté, on redirige vers la page de connexion
            return $this->redirectToRoute('app_login');
        }
        
        return $this->render('account/account.html.twig', [
            'user' => $user,
        ]);
    }
}
