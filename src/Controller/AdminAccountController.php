<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminAccountController extends AbstractController
{
    #[Route('/admin/login', name: 'app_admin_account_login')]
    public function login(AuthenticationUtils $utils): Response
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();
        return $this->render('admin/account/login.html.twig', [

            'hasError'=>$error!==null,
            'username'=>$username
            
        ]);
    }

    // Route pour la déconnexion
    #[Route('/admin/logout', name: 'app_admin_account_logout')]
    public function logout(): void
    {
        // La méthode de déconnexion est gérée automatiquement par Symfony
    }
}
