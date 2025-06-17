<?php

namespace App\Controller\Admin;

use App\Repository\AvisRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
            'page_title' => 'Avis en attente de validation', 
        ]);
    }

   
    /**
     * Cette route et méthode va npous permettre de valider l'avis passager 
     */

    #[Route('/avis/{id}/valider', name: 'app_admin_employe_avis_valider')]
    public function validerAvis(AvisRepository $avisRepository): Response
    {
        // On récupère la liste des avis en attente de validation  via le TrajetRepository avec le findBy 

        $avisEnAttente = $avisRepository->findBy(['statut' => 'en_attente_validation'], ['date' => 'DESC']);
        
        
        return $this->render('admin/employe/avis_list.html.twig', [
            'avisList' => $avisEnAttente,
            'page_title' => 'Avis en attente de validation', 
        ]);
    }


































}
