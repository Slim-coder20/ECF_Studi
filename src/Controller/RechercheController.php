<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\TrajetSearchTypeForm;
use App\Model\TrajetSearch;
use App\Repository\TrajetRepository;

final class RechercheController extends AbstractController
{
    #[Route('/rechercher', name: 'app_search')]
    public function index(Request $request,TrajetRepository $trajetRepository): Response
    {  
       $search = new TrajetSearch();
       // instancier le formulaire de recherche  
       
       $form = $this->createForm(TrajetSearchTypeForm::class);
       // création du formulaire 
        
        $form->handleRequest($request);
        // la requête est traitée 

        // si le formulaire est soumis et valide, on peut traiter les données
        $trajets = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $trajets = $trajetRepository->findBySearch($search);
        
        } 
        
        return $this->render('recherche/search.html.twig', [
            'form' => $form->createView(),
            'trajets' => $trajets,
        ]);
    }
}
