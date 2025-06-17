<?php

namespace App\Controller\Admin;

use App\Repository\AvisRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Avis;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/admin/employe')]
#[IsGranted('ROLE_EMPLOYE')]
 class EmployeController extends AbstractController
{
 /**
  * Cette route va nous permettre les avis avec le statut en attente de validation 
  */


    #[Route('/avis/en-attente', name: 'app_admin_employe_avis_list')]
    public function listAvisEnAttente(AvisRepository $avisRepository): Response
    {
        // On récupère la liste des avis en attente de validation  via le TrajetRepository avec le findBy 

        $avisEnAttente = $avisRepository->findBy(['statut' => 'en_attente_validation'], ['date' => 'DESC']);
        
        
        return $this->render('admin/employe/avis_list.html.twig', [
            'avisList' => $avisEnAttente,
            
        ]);
    }

   
    /**
     * Cette route et méthode va npous permettre de valider l'avis passager 
     */

    #[Route('/avis/{id}/valider', name: 'app_admin_employe_avis_valider', methods:['POST'])]
    public function validerAvis(Request $request, Avis $avis, EntityManagerInterface $em): Response
    {

       // On vérifie le token CSRF popur la sécurité // 
       if($this->isCsrfTokenValid('valider'.$avis->getId(), $request->request->get('_token'))){
        
        $avis->setStatut('approuve');
        $em->flush();

    
    }else{
        $this->addFlash('error', 'Token CSRF invalide.');
    
    }
        
    return $this->redirectToRoute('app_admin_employe_avis_list');
 }

/**
 * Cette route et méthode va nous permettre de refuser un avis par l'employe
 */
#[Route('/avis/{id}/refuser', name: 'app_admin_employe_avis_refuser', methods: ['POST'])]
    public function refuserAvis(Request $request, Avis $avis, EntityManagerInterface $em): Response
    {

       // On vérifie le token CSRF popur la sécurité // 
       if($this->isCsrfTokenValid('valider'.$avis->getId(), $request->request->get('_token'))){
        
        $avis->setStatut('rejete');
        $em->flush();
        $this->addFlash('success', "L'avis a été refusé. ");
    
    }else{
        $this->addFlash('error', 'Token CSRF invalide.');
    
    }
        
    return $this->redirectToRoute('app_admin_employe_avis_list');
 }















}
