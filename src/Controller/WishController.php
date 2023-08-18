<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Form\WishType;
use App\Repository\UserRepository;
use App\Repository\WishRepository;
use App\Services\Censurator;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class WishController extends AbstractController
{
    #[Route(
        '/liste',
        name: 'liste'
    )]

    public function liste(
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
    public function detail(
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
    #[IsGranted('ROLE_USER')]
    public function idee(
        EntityManagerInterface $entityManager,
        Request $requete,
        UserRepository $userRepository,
        Censurator $censurator
    ): Response
    {
        $wish = new Wish();
//        $utilisateur = $userRepository->find($this->getUser()->getUserIdentifier());
//        $wish->setAuthors($utilisateur);

        $wishForm = $this->createForm(WishType::class, $wish);
        $wishForm->handleRequest($requete);

        if($wishForm->isSubmitted() && $wishForm->isValid()) { // isValid() envoi le form quand tous les champs sont remplis

            $wish->setTitle($censurator->purify($wish->getTitle())); // Censure des mots interdits dans le titre par le service Censurator
            $wish->setDescription($censurator->purify($wish->getDescription()));// Censure des mots interdits dans la description par le service Censurator

            $entityManager->persist($wish); //Prepare l'insertion dans la BDD
            $entityManager->flush(); //Envoie dans la BDD
            $this->addFlash('success', 'Idea successfully added ! !'); // Ajoute un message flash apres envoi du formulaire
            return $this->redirectToRoute('liste_detail', ['wish' => $wish->getId()]); // Redirection apres envoi du formulaire
        }

        return $this->render('liste/idee.html.twig', [
            "wishForm" => $wishForm->createView(),
        ]);
    }


    #[Route(
        '/supprimer/{wish}',
        name: 'supprimer',
        requirements: ["wish" => "\d+"]
    )]
    public function supprimer(
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

    // Création d'une API de récupération des wishes
    #[Route(
        '/api/wishes',
        name: '_wish_json'
    )]
    function apiWish(
        WishRepository $wishRepository,
        SerializerInterface $serializer
    ): Response
    {
        $wishes = $wishRepository->findAll();
        return new JsonResponse($serializer->serialize($wishes, 'json', // Serialisation en json
        ['groups' => 'wishes:read']),
        200,
        [],
        true,
        );
    }

}

