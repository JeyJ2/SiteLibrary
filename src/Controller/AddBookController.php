<?php

namespace App\Controller;

use DateTime;
use App\Entity\Book;
use App\Entity\User;
use App\Form\BookType;
use App\Service\Mailjet;
use App\Entity\Notification;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class AddBookController extends AbstractController
{
    /**
     * @Route("editor/add/book", name="app_add_book")
     */
    public function index(Request $request, EntityManagerInterface $manager, SluggerInterface $slugger, UserRepository $userRepository, Mailjet $mailjet ): Response
    {   
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
    

        if($form->isSubmitted() && $form->isValid()){
            $bookCover = $form->get('picture')->getData();
            if(!$bookCover){
                $this->addFlash('danger', 'Vous devez choisir un fichier');
                $form = $this->createForm(BookType::class, $book);
            }
            if($bookCover){
                $originalFilename = pathinfo($bookCover->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$bookCover->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $bookCover->move(
                        $this->getParameter('bookCover_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                    dd($e->getMessage()."problème lors de téléchargement");
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $book->setPicture($newFilename);
                $book->setCreatedAt(new DateTime());
                $manager->persist($book); // on prépare les données
                $manager->flush(); // on envoie les données vers BDD 
                $this->addFlash('success', 'Le livre a bien été ajouté');
                $notification = new Notification();
                $notification->setCreatedAt(new DateTime());
                $notification->setMessage("Le livre".$book->getTitle()." a bien été ajouté") ;
                $admins = $userRepository->findByRole('ROLE_ADMIN');
            
                foreach($admins as $admin){
                    $notification->addDestinataire($admin);
                    $mailjet->sendEmail($admin, "Le livre".$book->getTitle()." a bien été ajouté");
                }
                $manager->persist($notification);
                $manager->flush();
                $form = $this->createForm(BookType::class, new Book());
            }
        }



        return $this->render('add_book/index.html.twig', [
            'controller_name' => 'AddBookController',
            'formulaire' => $form->createView(),
        ]);
    }
}
