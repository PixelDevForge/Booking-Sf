<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AnnonceType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AdRepository;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class AdminAdController extends AbstractController
{
    #[Route('/admin/ads', name: 'app_admin_ads_list')]
    public function index(AdRepository $repo): Response
    {
        return $this->render('admin/ad/index.html.twig', [
            'ads'=>$repo->findAll()
            
        ]);
    }

    #[Route('/admin/ads/{id}/edit', name:'app_admin_ads_edit')]
    public function edit(Ad $ad, EntityManagerInterface $entityManager, Request $request):Response
    {

        $form = $this->createForm(AnnonceType::class,$ad);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($ad);
            $entityManager->flush();
            $this->addFlash('success',"L'annonce a été modifié.");
        }


        return $this->render('admin/ad/edit.html.twig',[
            'ad'=>$ad,
            'form'=>$form->createView()
            ]);

    }

    #[Route('/admin/ads/{id}/delete', name: 'app_admin_ads_delete')]
    public function delete(Ad $ad,EntityManagerInterface $entityManager)
    {
        if(count($ad->getBookings()) > 0){
            $this->addFlash('warning',"L'annonce <strong>{$ad->getTitle()}</strong> ne peut pas être supprimée car elle possède des réservations.");
        }else{
            $entityManager->remove($ad);
            $entityManager->flush();
            $this->addFlash('success',"L'annonce <strong>{$ad->getTitle()}</strong>a été supprimée.");
        }
        return $this->redirectToRoute('app_admin_ads_list');
    }

    #[Route('/admin/comments', name: 'app_admin_comments')]
    public function comments(CommentRepository $comment,AdRepository $repo):Response
    {
        return $this->render('admin/comment/index.html.twig', [
            'comments'=>$comment->findAll()
        ]);


    }

}
