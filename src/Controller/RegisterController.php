<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function index(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hash): Response
    {   
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted()&& $form->isValid()){
           $hashedPassword = $hash->hashPassword($user, $user->getPassword());
            // $password = $user->getPassword();
            // $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
             $user->setPassword($hashedPassword);
            $manager-> persist($user);
            $manager-> flush();
            $this->addFlash('success', 'Utilisateur a bien été ajouté');
            $form = $this->createForm(RegisterType::class, new User());
        }


        return $this->render('register/index.html.twig', [
            'controller_name' => 'RegisterController',
            'formulaire' => $form->createView(),
        ]);
    }
}
