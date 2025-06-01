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
             'vehicules' => $user->getVehicules(), 
        ]);
    }
    
    // cette route permet d'jouter un véhicule si l'utilsateur souhaite passer en mode conducteur // 
    
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
            
            // traiter l'image si elle est présente
            $imageFile = $form->get('photo')->getData();
            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('photos_directory'),
                    $newFilename
                );
                $vehicule->setPhoto($newFilename);
            }
            
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


    // cette route permet de modifier les informations concernat le véhicule créé par l'utilisateur // 
    #[Route('/compte/vehicule/modifier/{id}', name: 'app_account_vehicule_edit')]
    public function editVehicule(Request $request, Vehicule $vehicule, EntityManagerInterface $entityManagerInterface): Response 
    {
        // Vérifie que l'utilisateur est bien le propriétaire du véhicule// 
        $this->denyAccessUnlessGranted('IS_AUTHETICATED_FULLY');
         if ($vehicule->getProprietaire() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }
    
        $form = $this->createForm(VehiculeTypeForm::class, $vehicule);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            // Traite l'image si elle est présente
            $imageFile = $form->get('photo')->getData();
            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('photos_directory'),
                    $newFilename
                );
                $vehicule->setPhoto($newFilename);
            }
            
            $entityManagerInterface->flush();
            // Ajout d'un message flash pour informer l'utilisateur de la réussite de la modification
            $this->addFlash('success', 'Véhicule modifié avec succès !');
            // Redirection vers la page des véhicules après la modification
            return $this->redirectToRoute('app_account');
        
        
        }
    
        return $this->render('account/vehicule_edit.html.twig', [
            'form' => $form->createView(),
            
        ]);
    
    }

    // cette route permet de supprimer le véhicule créé par l'utilisateur //
    #[Route('/compte/vehicule/supprimer/{id}', name: 'app_account_vehicule_delete', methods: ['POST'])]
    public function deleteVehicule(Vehicule $vehicule, EntityManagerInterface $entityManagerInterface, Request $request): Response
    {
        // Vérifie que l'utilisateur est bien le propriétaire du véhicule
        if (!$vehicule->getProprietaire() || $vehicule->getProprietaire()->getId() !== $this->getUser()->getId()) {
        throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à supprimer ce véhicule.');
       }
        

         if($this->isCsrfTokenValid('delete'.$vehicule->getId(), $request->request->get('_token'))) {
            
            // Supprime le véhicule de la base de données
            $entityManagerInterface->remove($vehicule);
            $entityManagerInterface->flush();
             
            // Ajout d'un message flash pour informer l'utilisateur de la réussite de la suppression
            $this->addFlash('success', 'Véhicule supprimé avec succès !');
        }else {
            // Si le token CSRF n'est pas valide, on redirige vers la page des véhicules
            $this->addFlash('danger', 'Token CSRF invalide. Suppression annulée.');
        }

        // Redirection vers la page des véhicules après la suppression
        return $this->redirectToRoute('app_account');
    }
    
    
    
    
    
    
    
    
    
}















