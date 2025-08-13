<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// Permet d’ouvrir la route au public sans auth :
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class AboutController extends AbstractController
{
    #[Route('/about', name: 'app_about', methods: ['GET'])]
    #[IsGranted('PUBLIC_ACCESS')]
    public function __invoke(): Response
    {

        return $this->render('about/index.html.twig', [
            'project_name' => 'LDVEH API',
            'buymeacoffee_slug' => 'Yazii',
        ]);
    }
}
