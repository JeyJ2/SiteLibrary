<?php

namespace App\Controller;

use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile", name="app_profile")
     */
    public function index(EntityManagerInterface $manager): Response
    {   
        $notifications=[];
        if($this->isGranted('ROLE_ADMIN')){
            $notifications = $manager->getRepository(Notification::class)->findAll();
        }
        return $this->render('profile/index.html.twig', [
            'notifications' => $notifications
        ]);
    }

    // /**
    //  * @Route("/redirectTo", name="app_redirect")
    //  */
    // public function redirect(): Response 
    // {   
    //     // autre méthode pour redirection lors de l'authentification (à mettre app_redirect)
    //     if($this->isGranted('ROLE_ADMIN')){
    //         return $this->redirectToRoute('app_dashboard');
    //     }else{
    //         return $this->redirectToRoute('app_profile');
    //     }
    // }
}
