<?php

// Déclare le namespace du contrôleur
namespace App\Controller;

// Importation des classes nécessaires
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
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

// Déclare la classe AccountController qui étend AbstractController
class AccountController extends AbstractController
{
    // Route pour la page de connexion
    #[Route('/login', name: 'app_account_login')]
    public function login(AuthenticationUtils $utils): Response
    {
        // Récupère la dernière erreur de connexion
        $error = $utils->getLastAuthenticationError();
        // Récupère le dernier nom d'utilisateur saisi
        $username = $utils->getLastUsername();
        
        // Rend la vue de la page de connexion avec les variables nécessaires
        return $this->render('account/login.html.twig',[
            'hasError' => $error !== null,
            'username' => $username            
        ]);
    }

    // Route pour la déconnexion
    #[Route('/logout', name: 'app_account_logout')]
    public function logout(): void
    {
        // La méthode de déconnexion est gérée automatiquement par Symfony
    }

    // Route pour l'inscription
    #[Route('/register', name: 'app_account_register')]
    public function register(Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $entityManager): Response
    {
        // Crée un nouvel utilisateur
        $user = new User();
        // Crée le formulaire d'inscription
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);
        
        // Vérifie si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Hache le mot de passe de l'utilisateur
            $hash = $hasher->hashPassword($user, $user->getHash());
            $user->setHash($hash);
            
            // Persiste l'utilisateur en base de données
            $entityManager->persist($user);
            $entityManager->flush();
            
            // Ajoute un message flash de succès
            $this->addFlash('success', 'Votre compte a été créé !');
            
            // Redirige vers la page de connexion
            return $this->redirectToRoute('app_account_login');
        }

        // Rend la vue de la page d'inscription avec le formulaire
        return $this->render('account/register.html.twig',[
            'form' => $form->createView()
        ]);
    }

    // Route pour la page de profil
    #[Route('/account/profile', name: 'app_account_profile')]
    #[IsGranted('ROLE_USER')]
    public function profile(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupère l'utilisateur actuel
        $user = $this->getUser();
        // Crée le formulaire de modification du compte
        $form = $this->createForm(AccountType::class, $user);
        $form->handleRequest($request);
        
        // Vérifie si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Persiste les modifications en base de données
            $entityManager->persist($user);
            $entityManager->flush();
            
            // Ajoute un message flash de succès
            $this->addFlash('success', 'Votre compte a été modifié !');
            
            // Redirige vers la page de connexion
            return $this->redirectToRoute('app_account_login');
        }

        // Rend la vue de la page de profil avec le formulaire
        return $this->render('account/profile.html.twig',[
            'form' => $form->createView()
        ]);
    }

    // Route pour la page de modification du mot de passe
    #[Route('/account/update-password', name: 'app_account_password')]
    #[IsGranted('ROLE_USER')]
    public function changePassword(Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $entityManager): Response
    {
        // Crée une nouvelle instance de PasswordUpdate
        $passwordUpdate = new PasswordUpdate();
        // Crée le formulaire de modification de mot de passe
        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);
        $form->handleRequest($request);
        // Récupère l'utilisateur actuel
        $user = $this->getUser();
        
        // Vérifie si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifie si l'ancien mot de passe est correct
            if (!$hasher->isPasswordValid($user, $passwordUpdate->getOldPassword())) {
                // Ajoute une erreur de formulaire si l'ancien mot de passe est incorrect
                $form->get('oldPassword')->addError(new FormError('Le mot de passe actuel est incorrect.'));
            } else {
                // Hache et met à jour le nouveau mot de passe
                $newPassword = $passwordUpdate->getNewPassword();
                $hash = $hasher->hashPassword($user, $newPassword);
                $user->setHash($hash);
                
                // Persiste les modifications en base de données
                $entityManager->persist($user);
                $entityManager->flush();
                
                // Ajoute un message flash de succès
                $this->addFlash('success', 'Le mot de passe a été mis à jour avec succès.');
                
                // Redirige vers la page de profil
                return $this->redirectToRoute('app_account_profile');
            }
        }

        // Rend la vue de la page de modification de mot de passe avec le formulaire
        return $this->render('account/password.html.twig',[
            'form' => $form->createView()
        ]);
    }

    // Route pour la page d'accueil du compte utilisateur
    #[Route('/account', name: 'app_account_home')]
    #[IsGranted('ROLE_USER')]
    public function myAccount(): Response
    {
        // Rend la vue de la page d'accueil du compte utilisateur avec les informations de l'utilisateur actuel
        return $this->render('user/index.html.twig', ['user' => $this->getUser()]);
    }
}
