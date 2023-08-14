<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'main.home.html.php')]
    public function home(): Response
    {
        return $this->render('main/home.html.twig'
        );
    }

    #[Route('/about_us', name: 'about_us')]
    public function home2(): Response

    {
        $jsonData = file_get_contents('json/team.json');

        // Décoder les données JSON en une structure PHP (tableau associatif ici)
        $data = json_decode($jsonData, true);

        // Vérifier si la lecture et le décodage se sont bien déroulés
        if ($data === null) {
            die('Erreur lors du traitement du JSON.');
        }
        dump($data);

        return $this->render('/about_us.html.twig',
            compact('data')
        );
    }
}
