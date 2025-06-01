<?php

namespace App\Controller;

use App\Entity\Vehicule;
use Dom\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\VehiculeTypeForm;

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
    
    
    #[Route('/compte/vehicules', name: 'app_account_vehicules')]
    public function vehicules(Request $request, EntityManagerInterface $entityManager): Response

    {
        $user = $this->getUser();
        if (!$user) {
            // Si l'utilisateur n'est pas connecté, on redirige vers la page de connexion
            return $this->redirectToRoute('app_login');
        }
        $vehicule = new Vehicule();
        $form = $this->createForm(VehiculeTypeForm::class, $vehicule);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $vehicule->setProprietaire($user);
            $entityManager->persist($vehicule);
            $entityManager->flush();

            // Ajout d'un message flash pour informer l'utilisateur de la réussite de l'ajout
            $this->addFlash('success', 'Véhicule ajouté avec succès !');

            // Redirection vers la page des véhicules après l'ajout
            return $this->redirectToRoute('app_account');
        }

        
        return $this->render('account/vehicules.html.twig', [
            'form' => $form->createView(),
        ]);
    }

















}
