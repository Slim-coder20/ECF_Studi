<?php 

// src/Controller/RechercheController.php

namespace App\Controller;

use App\Form\TrajetSearchTypeForm;
use App\Model\TrajetSearch;
use App\Repository\TrajetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RechercheController extends AbstractController
{
    #[Route('/rechercher', name: 'app_search')]
    public function index(Request $request, TrajetRepository $trajetRepository): Response
    {   
       
        $search = new TrajetSearch();
        $form = $this->createForm(TrajetSearchTypeForm::class, $search);
        $form->handleRequest($request);
        
        //dd(get_class($form));
        
         $trajets = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $trajets = $trajetRepository->findBySearch($search);
        }

        return $this->render('recherche/search.html.twig', [
            'form' => $form, 
            'trajets' => $trajets,
        ]);
    }
}
