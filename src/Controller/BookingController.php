<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\Comment;
use App\Form\BookingType;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookingController extends AbstractController
{
    #[Route('/ads/{slug}/book', name: 'app_booking_create')]
    #[IsGranted('ROLE_USER')]
    public function book(Request $request, Ad $ad, EntityManagerInterface $entityManager): Response
    {
        $booking = new Booking();
        $form = $this->createForm(BookingType::class,$booking);
        $form->handleRequest($request);

        // Vérifie si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
        
            $user = $this->getUser();
            $booking->setBooker($user)
                    ->setAd($ad);
            
            //

            if(!$booking->isBookabledays()){
                $this->addFlash("warning","Dates indisponibles");
            }else{
                // Persiste les modifications de la reservation en base de données
                $entityManager->persist($booking);
                $entityManager->flush();

                return $this->redirectToRoute('app_booking_show',['id'=>$booking->getId(),'alert'=>true]); 
            }

        }
            // Ajoute un message flash de succès
            //$this->addFlash('success', "Séjour réservé");
        return $this->render('booking/book.html.twig', [
            'ad'=>$ad,
            'form'=>$form->createView()
        ]);
    }

    #[Route('/booking/{id}', name: 'app_booking_show')]
    public function show(Booking $booking,Request $request, EntityManagerInterface $entityManager):Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class,$comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        
            $comment->setAd($booking->getAd())
                    ->setAuthor($this->getUser());

        
        

            $entityManager->persist($comment);
            $entityManager->flush();
            $this->addFlash("success","Commentaire enrgistré.");
        
        }


        return $this->render("booking/show.html.twig",['booking'=>$booking,'form'=>$form->createView()]);
    }
}
