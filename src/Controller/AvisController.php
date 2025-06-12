<?php

namespace App\Controller;
use App\Entity\Avis; 
use App\Entity\Participation;
use App\Repository\AvisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\AvisTypeForm;

#[Route('/avis')]
final class AvisController extends AbstractController
{
   #[Route('/participation/{id}/nouveau', name: 'app_avis_new', methods: ['GET', 'POST'])]
    public function avis_new(Request $request, EntityManagerInterface $em, Participation $participation, AvisRepository $avisRepository): Response
    {
    
    // Je commence par vérifier que l'utilisateur est bien connecté // 
    
    /** @var \App\Entity\User $currentUser */
     $currentUser = $this->getUser(); // ça permet de recupérer le user connecté actullement // 

    // 1. Vérification d'autorisation : L'utilisateur connecté est-il le passager de cette participation ?
    if ($participation->getPassager() !== $currentUser) {
        $this->addFlash('error', 'Vous n\'êtes pas autorisé à laisser un avis pour cette participation.');
        return $this->redirectToRoute('app_home'); 
    }

    //2. On vérifie le statut de la participation : le statut doit être en statut terminé pour pouvoir envoyer un avis // 
     if ($participation->getStatut() !== 'en_attente_validation_trajet') {
            $this->addFlash('warning', 'Vous ne pouvez pas laisser d\'avis pour ce trajet actuellement (statut incorrect).');
            return $this->redirectToRoute('app_account_historique_passager'); 
        
        }
    
    // 3. Vérification : Un avis a-t-il déjà été soumis pour ce trajet par cet auteur ?
    
    $trajetConcerne = $participation->getTrajet();
    
    if(!$trajetConcerne){
    
    $this->addFlash('error', 'impossible de trouver le trajet associé à cette participation'); 
    return $this->redirectToRoute('app_home');
    
    }

    $existingAvis = $avisRepository->findByOne([
       
        'auteur' => $currentUser,
        'trajet' => $trajetConcerne
        
    ]);

    if($existingAvis){
        $this->addFlash('info', 'Vous avez déjà sounmis un avis pour ce trajet');
        return $this->redirectToRoute('app_account_historique_passager');
    
    }
    // On instancie l'objet avis // 
    
    $avis = new Avis();
        
    $avis->setAuteur($currentUser);
    $avis->setTrajet($trajetConcerne);
    $avis->setChauffeur($trajetConcerne->getChauffeur());
    $avis->setDate(new \DateTime());
        
    // On créé le formulaire popur envoyer la note et le commentaire du participant // 
    
    $form = $this->createForm(AvisTypeForm::class, $avis);
    $form->handleRequest($request);
    if($form->isSubmitted() && $form->isValid()){
    
        
    
    
    
    }
    
    
    
    
    
    
    
    
    
    
        return $this->render('avis/avis.html.twig', [
            'participation' => $participation,
            'trajet' => $trajetConcerne,
            'formAvis' => $form->createView(),
        ]);
    }









}
