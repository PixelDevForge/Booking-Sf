<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Form\RegisterType;
use App\Entity\PasswordUpdate;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
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


    #[Route('/account/profile', name:'app_account_profile')]
    public function profile(Request $request,EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(AccountType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
           
            
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success','votre compte a été modifié !');
            return $this->redirectToRoute('app_account_login');

        }

        return $this->render('account/profile.html.twig',[
            'form'=>$form->createView()
            ]);
    }

    #[Route('/account/update-password', name:'app_account_password')]

    public function changePassword(Request $request,UserPasswordHasherInterface $hasher,EntityManagerInterface $entityManager): Response
    {
       
        $passwordUpdate = new PasswordUpdate();
        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);
        $form->handleRequest($request);
        $user = $this->getUser();
        
        if ($form->isSubmitted() && $form->isValid()) {
           
            if (!$hasher->isPasswordValid($user, $passwordUpdate->getOldPassword())) {

                //$this->addFlash('warning', 'Le mot de passe actuel est incorrect.');

                $form->get('oldPassword')->addError(new FormError('Le mot de passe actuel est incorrect.'));

            }else{
                $newPassword = $passwordUpdate->getNewPassword();
                $hash = $hasher->hashPassword($user, $newPassword);
                $user->setHash($hash);
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('success', 'Le mot de passe a été mis à jour avec succès.');
                return $this->redirectToRoute('app_account_profile');
            }

       }


        return $this->render('account/password.html.twig',[
            'form'=>$form->createView()
            ]);

    }

    #[Route('/account', name:'app_account_home')]
    public function myAccount(): Response
    {
        return $this->render('user/index.html.twig',['user' => $this->getUser()]);
    }

}