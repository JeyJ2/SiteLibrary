<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AddUserController extends AbstractController
{
    /**
     * @Route("/add/user", name="app_add_user")
     */
    public function index(Request $request, EntityManagerInterface $manager): Response
    {   
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() ){
            $user->setCreatedAt(new \DateTime());
            $manager->persist($user); // on prépare les données
            $manager->flush(); // on envoie les données vers BDD
            $this->addFlash('success', 'L\'utilisateur a bien été ajouté');
            $form = $this->createForm(UserType::class);
        }


        return $this->render('add_user/index.html.twig', [
            'formulaire' => $form->createView(),
        ]);
    }
}
