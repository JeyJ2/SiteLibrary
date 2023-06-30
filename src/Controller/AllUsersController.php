<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AllUsersController extends AbstractController
{
    /**
     * @Route("/all/users", name="app_all_users")
     */
    public function index(EntityManagerInterface $manager): Response
    {   
        $users = $manager->getRepository(User::class)->findAll();

        return $this->render('all_users/index.html.twig', [
            'users' => $users,
        ]);
    }
}
