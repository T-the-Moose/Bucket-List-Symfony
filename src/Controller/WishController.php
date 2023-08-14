<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Form\WishType;
use App\Repository\WishRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class WishController extends AbstractController
{
    #[Route(
        '/liste',
        name: 'liste'
    )]

    public function index(
        WishRepository $wishRepository,
        HttpClientInterface $client // Injection de dépendance pour Appel API Chuck Norris
    ): Response
    {
        $wishes = $wishRepository->findAllPublished(
            ["isPublished" => true],
        );
        //Appel API Chuck Norris
        $reponse = $client->request(
            'GET',
            'https://chuckn.neant.be/api/rand'
        );
        $blagues = $reponse->toArray();
//        dd($blagues);
        return $this->render('list/liste.html.twig',
            compact('wishes', 'blagues')
        );
    }

    #[Route(
        '/detail/{wish}',
        name: 'liste_detail',
        requirements: ["wish" => "\d+"]
    )]
    public function index2(
        WishRepository $wishRepository,
        $wish
    ): Response
    {
        $wishes = $wishRepository->find($wish);
        return $this->render('liste/detail.html.twig',
        compact('wishes')
        );
    }

    #[Route(
        '/idee',
        name: 'idee',
    )]
    public function formulaire(
        EntityManagerInterface $entityManager,
        Request $requete,
    ): Response
    {
        $wish = new Wish();

        $wishForm = $this->createForm(WishType::class, $wish);

        $wishForm->handleRequest($requete);

        if($wishForm->isSubmitted() && $wishForm->isValid()) { // isValid() envoi le form quand tous les champs sont remplis
            $entityManager->persist($wish); //Prepare l'insertion dans la BDD
            $entityManager->flush(); //Envoie dans la BDD
            $this->addFlash('success', 'Idea successfully added ! !'); // Ajoute un message flash apres envoi du formulaire
            return $this->redirectToRoute('liste_detail', ['wish' => $wish->getId()]); // Redirection apres envoi du formulaire
        }

        return $this->render('liste/idee.html.twig', [
            "wishForm" => $wishForm->createView(),
        ], );
    }


    #[Route(
        '/supprimer/{wish}',
        name: 'supprimer',
        requirements: ["wish" => "\d+"]
    )]
    public function deleteWish(
        WishRepository $wishRepository,
        EntityManagerInterface $entityManager,
        $wish
    ): Response {
        $wish = $wishRepository->find($wish);

        if (!$wish) {
            throw $this->createNotFoundException('Wish non trouvé');
        }
        $entityManager->remove($wish); // Suppression d'un objet
        $entityManager->flush();
        $this->addFlash('success', 'Wish supprimé avec succès');
        return $this->redirectToRoute('liste');
    }

}

