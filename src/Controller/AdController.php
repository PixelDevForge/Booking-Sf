<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Image;
use App\Form\AnnonceType;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/ads', name: 'app_ads_list')]
    public function index(AdRepository $adRepository) : Response
    {
        $ads = $adRepository->findAll(); // Récupère toutes les annonces

        return $this->render('ad/index.html.twig', [
            'controller_name' => 'Les annonces',
            'ads' => $ads, // Passe les annonces au template
        ]);
    }

    #[Route('/ads/new', name:'ads_create')]
    public function create(Request $request) : Response
    {
        $ad = new Ad();
        /*$images = new Image();
        $images->setUrl('https://picsum.photos/1000/500')
               ->setCaption('image 1');
        $ad->addImage($images);*/
        $form = $this->createForm(AnnonceType::class, $ad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            foreach ($ad->getImages() as $image) {
                $image->setAd($ad);
                $this->entityManager->persist($image);
            }

            $this->entityManager->persist($ad);
            $this->entityManager->flush();
            $this->addFlash('success',"Annonce <strong>{$ad->getTitle()}</strong> ajouté");

            return $this->redirectToRoute('ads_single', ['slug' => $ad->getSlug()]);
        }


        return $this->render('ad/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/ads/{slug}', name:'ads_single')]
    public function show($slug, AdRepository $adRepository) : Response
    {
        $ad = $adRepository->findOneBySlug($slug);

        if (!$ad) {
            throw $this->createNotFoundException('L\'annonce demandée n\'existe pas.');
        }

        return $this->render('ad/show.html.twig',[
            'ad' => $ad,
        ]);
    }


    #[Route('/ads/{slug}/edit', name: 'ads_edit')]
    public function edit(Request $request, Ad $ad, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AnnonceType::class, $ad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($ad->getImages() as $image) {
                $image->setAd($ad);
                dump($image);
                $entityManager->persist($image);
            }
            $entityManager->persist($ad);
            $entityManager->flush();
            $this->addFlash('success', "Annonce modifiée");

            return $this->redirectToRoute('ads_single', ['slug' => $ad->getSlug()]);
        }

        return $this->render('ad/edit.html.twig', [
            'form' => $form->createView(),
            'ad' => $ad
        ]);
    }




}
