<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AccountController extends AbstractController
{
   #[Route('/login', name: 'app_account_login')]
    public function login(AuthenticationUtils $utils): Response
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();
        return $this->render('account/login.html.twig',[
            'hasError'=>$error!==null,
            'username'=>$username            
        ]);
    }

    #[Route('/logout', name: 'app_account_logout')]
    public function logout(): void
    {
        
    }

    #[Route('/register', name:'app_account_register')]
    public function register(Request $request,UserPasswordHasherInterface $hasher,EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $hash = $hasher->hashPassword($user, $user->getHash());
            $user->setHash($hash);
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success','votre compte a été créé !');
            return $this->redirectToRoute('app_account_login');

        }


        return $this->render('account/register.html.twig',[
            'form'=>$form->createView()
            ]);
    }
}
