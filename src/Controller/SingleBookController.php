<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Comment;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class SingleBookController extends AbstractController
{
    /**
     * @Route("/single/book/{id}", name="app_single_book")   
     */
    public function index($id,EntityManagerInterface $manager, Request $request): Response
    {   
        $book = $manager->getRepository(Book::class)->find($id);
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if($form->isSubmitted()){
            $comment->setBook($book);
            $comment->setCreatedAt(new \DateTime());
            $manager->persist($comment);
            $manager->flush();
            return $this->redirectToRoute('app_single_book', ['id' => $id]);
        }

        return $this->render('single_book/index.html.twig', [
            'book' => $book,
            'formulaire' => $form->createView(),
        ]);
    }

    /**
     * @Route("/single/book/remove/{id}", name="app_single_book_remove")   
     */
    public function remove($id, EntityManagerInterface $manager)
    {   
        $book = $manager->getRepository(Book::class)->find($id);
        $manager->remove($book);
        $manager->flush();
        return $this->redirectToRoute('app_home');
    }
}
