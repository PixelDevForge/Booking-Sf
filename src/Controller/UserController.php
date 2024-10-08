<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    #[Route('/user/{slug}', name: 'app_user_show')]
    public function index(User $user): Response
    {

        return $this->render('user/index.html.twig', [
            'user' => $user,
        ]);
    }
}
