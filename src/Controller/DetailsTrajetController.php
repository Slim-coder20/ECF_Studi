<?php

namespace App\Controller;
use App\Entity\Trajet;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DetailsTrajetController extends AbstractController
{
    #[Route('/trajet/{id}', name: 'app_details_trajet')]
    public function index(Trajet $trajet): Response
    {
        return $this->render('details_trajet/show.html.twig', [
            'trajet' => $trajet,
        ]);
    }
}
