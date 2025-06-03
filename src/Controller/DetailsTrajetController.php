<?php

namespace App\Controller;
use App\Entity\Trajet;
use App\Entity\Participation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
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
        
        // On stocke l'intention de participer avant connexion // 
        if(!$this->getUser()) {
            $session = $request->getSession();
            $session->set('intended_trajet', $trajet->getId());
            return $this->redirectToRoute('app_login');
        }
        
        
        $user = $this->getUser(); 
        // On vérifie que l'utilisateur est connecté avant de lui permettre de participer à un trajet // 
       if(!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour participer à un trajet.');
            return $this->redirectToRoute('app_login');
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
            $em->persist($participation);
            
            $user->setCredits($user->getCredits() - $trajet->getPrix());
            // on retire le prix du trajet des crédits de l'utilisateur //
            $trajet->setNbPlaces($trajet->getNbPlaces() - 1);
            // on retire une place du trajet //

            $em->persist($participation);
            $em->flush();

        $this->addFlash('success', 'Votre participation est confirmée !');
        return $this->redirectToRoute('app_account');
        
        }
        
        
        return $this->render('details_trajet/confirmation.html.twig', [
            'trajet' => $trajet,
            'user' => $user,
        ]);
    }

    // Cette route permet de proposer un trajet depuis l'espace utilisateur //
    #[Route('compte/trajet/ajouter', name: 'app_add_trajet')]
    public function ajouterTrajet(Request $request, EntityManagerInterface $em ): Response 
    
    {
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
