<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Image;
use App\Form\AnnonceType;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class AdController extends AbstractController
{
    private $entityManager;

    // Constructeur pour injecter l'EntityManager
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    // Route pour afficher la liste des annonces
    #[Route('/ads', name: 'app_ads_list')]
    public function index(AdRepository $adRepository) : Response
    {
        $ads = $adRepository->findAll(); // Récupère toutes les annonces

        // Rend la vue avec les annonces
        return $this->render('ad/index.html.twig', [
            'controller_name' => 'Les annonces',
            'ads' => $ads, // Passe les annonces au template
        ]);
    }

    // Route pour créer une nouvelle annonce
    #[Route('/ads/new', name:'ads_create')]
    // Restriction d'accé au role USER
    #[IsGranted('ROLE_USER')]
    public function create(Request $request) : Response
    {
        $ad = new Ad();
        $form = $this->createForm(AnnonceType::class, $ad);
        $form->handleRequest($request);

        // Vérifie si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {

            // Associe chaque image à l'annonce et persiste en base de données
            foreach ($ad->getImages() as $image) {
                $image->setAd($ad);
                $this->entityManager->persist($image);
            }

            // Associe l'auteur à l'annonce
            $ad->setAuthor($this->getUser());

            // Persiste l'annonce en base de données
            $this->entityManager->persist($ad);
            $this->entityManager->flush();

            // Ajoute un message flash de succès
            $this->addFlash('success', "Annonce <strong>{$ad->getTitle()}</strong> ajoutée");

            // Redirige vers la page de l'annonce créée
            return $this->redirectToRoute('ads_single', ['slug' => $ad->getSlug()]);
        }

        // Rend la vue pour créer une nouvelle annonce avec le formulaire
        return $this->render('ad/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Route pour afficher une annonce spécifique
    #[Route('/ads/{slug}', name:'ads_single')]
    public function show($slug, AdRepository $adRepository) : Response
    {
        $ad = $adRepository->findOneBySlug($slug);

        // Vérifie si l'annonce existe
        if (!$ad) {
            throw $this->createNotFoundException('L\'annonce demandée n\'existe pas.');
        }

        // Rend la vue de l'annonce avec ses détails
        return $this->render('ad/show.html.twig', [
            'ad' => $ad,
        ]);
    }

    // Route pour éditer une annonce
    #[Route('/ads/{slug}/edit', name: 'ads_edit')]
    // Restriction d'édition
    /**
    * Contrôle d'accès : Vérifie si l'utilisateur connecté est l'auteur de l'annonce.
    * Seuls les utilisateurs ayant le rôle 'ROLE_USER' et qui sont également l'auteur de l'annonce peuvent accéder à la ressource.
    * Sinon, affiche un message d'erreur personnalisé.
    */
    //#[Security("is_granted('ROLE_USER') and user === ad.getAuthor()", message: "Vous n'êtes pas l'auteur de l'annonce")]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, Ad $ad, EntityManagerInterface $entityManager): Response
    {

         // Vérifie si l'utilisateur est l'auteur de l'annonce
         if ($this->getUser() !== $ad->getAuthor()) {
            throw $this->createAccessDeniedException("Vous n'êtes pas l'auteur de l'annonce");
        }


        $form = $this->createForm(AnnonceType::class, $ad);
        $form->handleRequest($request);

        // Vérifie si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Associe chaque image à l'annonce et persiste en base de données
            foreach ($ad->getImages() as $image) {
                $image->setAd($ad);
                $entityManager->persist($image);
            }

            // Persiste les modifications de l'annonce en base de données
            $entityManager->persist($ad);
            $entityManager->flush();

            // Ajoute un message flash de succès
            $this->addFlash('success', "Annonce modifiée");

            // Redirige vers la page de l'annonce modifiée
            return $this->redirectToRoute('ads_single', ['slug' => $ad->getSlug()]);
        }

        // Rend la vue pour éditer une annonce avec le formulaire
        return $this->render('ad/edit.html.twig', [
            'form' => $form->createView(),
            'ad' => $ad,
        ]);
    }
    // Route pour supprimer une annonce
    #[Route('/ads/{slug}/delete', name: 'ads_delete')]
    #[IsGranted('ROLE_USER')]
    public function delete(Ad $ad,EntityManagerInterface $entityManager)
    {

        // Vérifie si l'utilisateur est l'auteur de l'annonce
        if ($this->getUser() !== $ad->getAuthor()) {
            
            // Ajoute un message flash d'erreur
            $this->addFlash('danger', "Vous n'êtes pas l'auteur de l'annonce");
            return $this->redirectToRoute('app_ads_list');  
        }else{
            $entityManager->remove($ad);
            $entityManager->flush();
            // Ajoute un message flash de succès
            $this->addFlash('success', "Annonce <em>{$ad->getTitle()}</em> a été supprimée");
            return $this->redirectToRoute('app_ads_list');  
        }
            
        
        
        
    }
}
