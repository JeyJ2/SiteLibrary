<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\SearchType;
use App\Repository\BookRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(EntityManagerInterface $manager, BookRepository $bookRepository, UserRepository $userRepository, Request $request): Response
    {  
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        $books= $manager->getRepository(Book::class)->findAll();

        if($form ->isSubmitted() && $form->isValid()){
            $titre = $form->get('titre')->getData();
            $books = $bookRepository->findByTitle($titre);
        }
    
        $total = $bookRepository->nbrLivres();


      /* partie test uniquement
        //fonctions repository
      
        $livres = $bookRepository->findByTitle('les misérables');
        $users = $userRepository ->findByFirstname('jey');
        //dd($livres);
        //dd($users);

    */
        return $this->render('home/index.html.twig', [
            'books' => $books,
            'total' => $total,
            'formulaire' => $form->createView(),
        ]);
    }

    
}
