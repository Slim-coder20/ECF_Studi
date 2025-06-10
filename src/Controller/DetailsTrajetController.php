<?php

namespace App\Controller;
use App\Entity\Trajet;
use App\Entity\Participation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
 use Symfony\Component\Security\Http\Attribute\IsGranted;

use App\Form\TrajetTypeForm;

final class DetailsTrajetController extends AbstractController
{
    #[Route('/trajet/{id}', name: 'app_details_trajet')]
    public function index(Trajet $trajet): Response
    {
        return $this->render('details_trajet/show.html.twig', [
            'trajet' => $trajet,
        ]);
    }

    // Cette route va nous permettre de participer à un trajet via la page détails du trajet//  
    #[Route('/trajet/{id}/participer', name: 'app_participer_trajet')]
    public function participer(Trajet $trajet, Request $request, EntityManagerInterface $em): Response
    {   
         /** @var \App\Entity\User|null $user */ 
         $user = $this->getUser(); 
        // On stocke l'intention de participer avant connexion // 
        if(!$this->getUser()) {
            $session = $request->getSession();
            $session->set('intended_trajet', $trajet->getId());
            return $this->redirectToRoute('app_login');
        }
        
        // On vérifie si l'utilisateur est le chauffeur du trajet //
        if($trajet->getChauffeur() == $user){
            $this->addFlash('error', 'Vous ne pouvez pas participer à votre propre trajet en tant que passager.');
            return $this->redirectToRoute('app_login');
        }
        
        // on vérife si l'utilisateur participe )déjà // 

        foreach($trajet->getParticipations() as $existingParticipation){
         if($existingParticipation->getPassager() === $user){
            $this->addFlash('info', 'Vous participez déjà à ce trajet.');
            return $this->redirectToRoute('app_details_trajet', ['id' => $trajet->getId()]);
        }
        
        }
    
    
    // vérfier si il reste des places disponible // 
        if ($trajet->getNbPlaces() <= 0) {
            $this->addFlash('error', 'Il n\'y a plus de places disponibles pour ce trajet.');
            return $this->redirectToRoute('app_details_trajet', ['id' => $trajet->getId()]);
        }
        
        // on vérifie que l'utilisateur a bien du crédits pour valier sa participation // 
        if ($user->getCredits() < $trajet->getPrix()) {
            $this->addFlash('error', 'Vous n\'avez pas assez de crédits pour participer à ce trajet.');
            return $this->redirectToRoute('app_details_trajet', ['id' => $trajet->getId()]);
        }
        
        // La partie confirmation de la participation // 
        if($request->isMethod('POST') && $request->request->get('confirm') === 'yes') {

            $participation = new Participation();
            // on instancie la participation // 
            $participation->setTrajet($trajet);
            // on associe le trajet à la participation // 
            $participation->setPassager($user);
            // on associe l'utilisateur à la participation //
            $participation->setDateParticipation(new \DateTime());
            // on associe la date de participation à la participation //
            $participation->setPrixPaye($trajet->getPrix());
            // on associe le prix payé à la participation //    
            $participation->setStatut('confirmée'); 
            // on défini le statut 
            $em->persist($participation);

            
            $user->setCredits($user->getCredits() - $trajet->getPrix());
            // on retire le prix du trajet des crédits de l'utilisateur //
            
            $trajet->setNbPlaces($trajet->getNbPlaces() - 1);
            // on retire une place du trajet //

            $em->flush();

        $this->addFlash('success', 'Votre participation est confirmée !');
        return $this->redirectToRoute('app_account_historique');
        
        }
        
        
        return $this->render('details_trajet/confirmation.html.twig', [
            'trajet' => $trajet,
            'user' => $user,
        ]);
    }

    // cette méthode nous permettra d'annuler la participation à un Trajet // 
     #[Route('/participation/{id}/annuler', name: 'app_participation_annuler', methods: ['POST'])]
     #[IsGranted('ROLE_USER')]
     public function annulerParticipation(Request $request, Participation $participation, EntityManagerInterface $em): Response
     {   
        // Récupaération de l'utilisateur connecté //
         /** @var \App\Entity\User $user */
         $user = $this->getUser();

        //On vérifie les droits d'annulation du trajet //
        if($participation->getPassager() !== $user){
            $this->addFlash('error', "Vous n'êtes pas autoriser à annuler cette participation."); 
            return $this->redirectToRoute('app_account_historique'); 
        }
        // Mise en place du token CSRF pour la sécurité de la requete envoyé pour l'annulation de participation // 
        $token = $request->request->get('_token');
        // On récupère le token sounis deouis le formulaire dans le template historique_passager.html.twig // 
        if(!$this->isCsrfTokenValid('annuler_participation'.$participation->getId(),$token)){
            // Le nom du token 'annuler_participation' . $participation->getId() doit correspondre
            // à celui généré dans le template Twig.
            $this->addFlash('error', "Vous n'êtes pas autorisé à annuler cette participation.");
            return $this->redirectToRoute('app_account_historique'); 
        
        }
        // Récupération du trajet associé à la participation 
        $trajet = $participation->getTrajet();
         
        // les conditions d'annulation de participation // 
        // On vérifie si la participation est 'confirmé'et si le trajet est planifié ou coplet // 

        if($participation->getStatut() === 'confirmée' && $trajet->getStatut() === 'planifié' || $trajet->getStatut() === 'complet'){
            
            // Action si l'annulation est permise // 
            // mettre a jour le statut de la participation // 
            $participation->setStatut('annulee_par_passager');

            // recréditer l'utilisateur des prix payé pour la participation // 
            $user ->setCredits($user->getCredits() + $participation->getPrixPaye());

            //Augmenter le nombre de place disponible pour la participation // 
            $trajet->setNbPlaces($trajet->getNbPlaces() + 1);
            
            // Si le trajet était 'complet', il reevient 'planifier' car une place s'est liberé // 
             if ($trajet->getStatut() === 'complet') {
                $trajet->setStatut('planifié');
            }
            // sauvegarde tous les changements en base de donnée // 
            $em->flush();
            $this->addFlash('success', 'Votre participation a bien été annulée.');
        }else{
        
         $this->addFlash('warning', 'Cette participation ne peut plus être annulée (vérifiez le statut de la participation ou du trajet).');
        
        }
         
        return $this->redirectToRoute('app_account_historique');
    }


// Cette route permet de proposer un trajet depuis l'espace utilisateur //
    #[Route('compte/trajet/ajouter', name: 'app_add_trajet')]
    public function ajouterTrajet(Request $request, EntityManagerInterface $em ): Response 
    
    {  /** @var \App\Entity\User|null $user */ 
      $user = $this->getUser(); 
      // Sécurité : On vérifie que l'utilisateur est bien connecté // 
      if(!$user){
        return $this->redirectToRoute('app_login'); 
    }

    if($user->getVehicules()->isEmpty()){
        $this->addFlash('warning', 'Voous devez enregistrer un véhicule dans votre espace avant de proposer un trajet .'); 
        return $this->redirectToRoute('app_account_vehicules'); 
    
    
    }
    // On instancie un nouveau Trajet // 

    $trajet = new Trajet(); 
    $trajet->setChauffeur($user); 
    $trajet->setStatut('planifié');

    // On créé le formulaire pour l'ajout de Trajet // 
    $form = $this->createForm(TrajetTypeForm::class, $trajet);
    $form->handleRequest($request); 

    // On vérifie que le formulaire est soumis et qu'il est bien valide // 
    if($form->isSubmitted() && $form->isValid()){

    
        $em->persist($trajet); 
        $em->flush(); 
        $this->addFlash('success','Votre trajet a été ajouté avec succés !'); 
        return $this->redirectToRoute('app_account'); 
    
    
    }
    return $this->render('account/trajet_add.html.twig', [
        'form' => $form->createView(),
    ]);
      
    
    
    
    }









}
