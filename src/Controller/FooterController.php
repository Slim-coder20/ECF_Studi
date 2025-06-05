<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FooterController extends AbstractController
{
    
    #[Route('/mentions-légale', name: 'app_legal_notice', methods: ['GET', 'POST'])]
    public function leaglNotice(): Response
    {
        return $this->render('footer/legal_notice.html.twig', []);
        
    }
    
    
    #[Route('/politique-de-confidentialité', name: 'app_cgv', methods: ['GET', 'POST'])]
    public function cgv(): Response
    {
        return $this->render('footer/cgv.html.twig', []);
    }
    
    
    #[Route('/cgu ', name: 'app_cgu', methods: ['GET', 'POST'])]
    public function cgu(): Response
    {
      return $this->render('footer/cgu.html.twig', []);
    }
}
