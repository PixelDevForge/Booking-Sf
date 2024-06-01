<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    public function index(AdRepository $adRepository): Response
    {
        
        $ads = $adRepository->findAll(); // Récupère toutes les annonces

        return $this->render('ad/index.html.twig', [
            'controller_name' => 'Les annonces',
            'ads' => $ads, // Passe les annonces au template
        ]);
    }

    #[Route('/ads/{slug}', name:'ads_single')]
    public function show($slug,AdRepository $adRepository): Response{

        return $this->render('ad/show.html.twig',[
            $ad = $adRepository->findOneBySlug($slug),
            'ad' => $ad,
        ]);

    }
}
