<?php

namespace App\Controller;
use App\Entity\Trajet;
use App\Entity\User;
use App\Service\EmailService; 
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/chauffeur/trajet')]
#[IsGranted('ROLE_USER')] 
 class TrajetDriverController extends AbstractController
{   
    // On créé la route et la méthode pour démarrer un trajety coté conducteur // 
    #[Route('/{id}/demarrer', name: 'app_trajet_demarrer', methods:['POST'])]
    public function demarrerTrajet(Request $request, EntityManagerInterface $em, Trajet $trajet): Response
    {    
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        
        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('demarrer_trajet' . $trajet->getId(), $token)) {
            $this->addFlash('error', 'Action non autorisée (Token CSRF invalide).');
            return $this->redirectToRoute('app_account_historique_chauffeur');
        }
        
        // On vérifie que l'utilisateur connecté est bien le chauffeur du trajet // 
        if($trajet->getChauffeur() !== $user)
        {
            $this->addFlash('error', "Vous n'êtes pas autorisé à démarrer ce trajet.");
            return $this->redirectToRoute('app_account_historique_chauffeur'); 
        } 

        // On vérifie le statut du trajet qui doit être soit planifie ou complet // 

        if(!in_array($trajet->getStatut(), ['planifié', 'complet']))
        {
            
           $this->addFlash('warning', 'Ce trajet ne peut pas être démarré (statut actuel : ' . $trajet->getStatut() . ').');
            return $this->redirectToRoute('app_account_historique_chauffeur');
        
        }    
        
        // On change le statut du trajet dans la base de donnée la table Trajet // 
            $trajet->setStatut('en_cours');
            $em->flush(); 
            return $this->redirectToRoute('app_account_historique_chauffeur');
     
    }

    // on créé la méthode et la rooute pour annuler un trajet par le chauffeur // 

    #[Route('/{id}/annuler', name: 'app_trajet_annuler_chauffeur', methods:['POST'])]
    public function annulerTrajet(Request $request, EntityManagerInterface $em, Trajet $trajet): Response
    {   
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
    
        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('annuler_trajet' . $trajet->getId(), $token)) {
            $this->addFlash('error', 'Action non autorisée (Token CSRF invalide).');
            return $this->redirectToRoute('app_account_historique_chauffeur');
        }

        // On vérifie que l'utilisateur connecté est bien le chauffeur du trajet // 
        if($trajet->getChauffeur() !== $user)
        {
            $this->addFlash('error', "Vous n'êtes pas autorisé à annuler ce trajet.");
            return $this->redirectToRoute('app_account_historique_chauffeur'); 
        } 
        
        // On vérfie les conditions pour l'annulation de trajet statut doit être 'planifié' ou 'complet' // 
        if (!in_array($trajet->getStatut(), ['planifié', 'complet'])) {
            $this->addFlash('warning', 'Ce trajet ne peut pas être annulé (statut actuel : ' . $trajet->getStatut() . ').');
            return $this->redirectToRoute('app_account_historique_chauffeur');
        }
        // changer le statut du trajet // 
         $trajet->setStatut('annulé_par_chauffeur');

        // Gérer les participations et remboursser les crédits // 
         foreach ($trajet->getParticipations() as $participation) {
            if ($participation->getStatut() === 'confirmée') {  
               
                $passager = $participation->getPassager();
                if ($passager) {
                    $passager->setCredit($passager->getCredit() + $participation->getPrixPaye());
                    $em->persist($passager); // S'assurer que les modifications sur le passager sont persistées
                }
                $participation->setStatut('trajet_annule'); // Nouveau statut pour la participation
                // Optionnel: Notifier le passager par email de l'annulation
                // $this->envoyerEmailAnnulationPassager($passager, $trajet);
                $em->persist($participation);
            }
        }
          $em->flush(); // Sauvegarde toutes les modifications (trajet, participations, passagers)
        
        $this->addFlash('success', 'Le trajet a été annulé avec succès. Les passagers ont été notifié et rembourssé.');
        return $this->redirectToRoute('app_account_historique_chauffeur');
    }

    // On créé une méthode pour permettre au chauffeur de terminer un trajet // 
    #[Route('/{id}/terminer', name: 'app_trajet_terminer_chauffeur', methods:['POST'])]
    public function terminerTrajetChauffeur(Request $request, EntityManagerInterface $em, Trajet $trajet, EmailService $emailService): Response 
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        
        // 1. Vérifier le token CSRF
        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('terminer_trajet_chauffeur' . $trajet->getId(), $token)) {
            $this->addFlash('error', 'Action non autorisée (Token CSRF invalide).');
            return $this->redirectToRoute('app_account_historique_chauffeur');
        }
        

        // 2. Vérifier que l'utilisateur connecté est bien le chauffeur du trajet
        if ($trajet->getChauffeur() !== $user) {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à terminer ce trajet.');
            return $this->redirectToRoute('app_account_historique_chauffeur');
        }

        // On vérifie que le statut du trajet permet de le terminer // 

        if ($trajet->getStatut() !== 'en_cours') {
            $this->addFlash('warning', 'Ce trajet ne peut pas être marqué comme terminé (statut actuel : ' . $trajet->getStatut() . ').');
            return $this->redirectToRoute('app_account_historique_chauffeur');
        }
        
        // changer le statut du trajet // 
        $trajet->setStatut('terminé');
        $em->persist($trajet);
        
        // Mise a jour le statut de participation et envoyer un mail de notofication pour les participants 

        foreach($trajet->getParticipations() as $participation){
            if($participation->getStatut() === 'confirmée'){
                $participation->setStatut('en_attente_validation_trajet');
                $em->persist($participation);

                $passager = $participation->getPassager();

                if($passager && $passager->getEmail()){
                    
                    $lienValidation = $this->generateUrl(
                      'app_home',
                      ['id_participation' => $participation->getId()],
                      UrlGeneratorInterface::ABSOLUTE_URL  
                    );
                    $subject = 'Votre Trajet Ecoride est terminé: Validez votre expérience !';
                    $template = 'emails/validation_trajet_passager.html.twig';
                    $context = [
                        'passagerNom' => $passager->getFirstName() ?? $passager->getLastName(),     
                        'trajet' => $trajet, 
                        'lien_validation' => $lienValidation,
                        
                    ];

                    try {
                        $emailService->sendTemplatedEmail(
                            $passager->getEmail(),
                            $subject,
                            $template, 
                            $context
                            
                         );
                         $this->addFlash('info', 'Un email de validation a été envoyé à'.$passager->getEmail());
                    
                    
                    
                    } catch (TransportExceptionInterface $e){
                    
                     $this->addFlash('error', 'Impossible d\'envoyer l\'email de validation à ' . $passager->getEmail() . '. Erreur: ' . $e->getMessage());
                    
                    
                    }
                }
            }
        }
        $em->flush();
        $this->addFlash('success', 'Le trajet "' . $trajet->getVilleDepart() . ' - ' . $trajet->getVilleArrivee() . '" a été marqué comme terminé. Les passagers concernés ont été notifiés.');
        return $this->redirectToRoute('app_account_historique_chauffeur');
    }
    
}
