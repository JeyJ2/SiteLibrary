<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class EditBookController extends AbstractController
{
    /**
     * @Route("/edit/book/{id}", name="app_edit_book")
     */
    public function index($id,Request $request, EntityManagerInterface $manager, SluggerInterface $slugger): Response
    {   
        $book = $manager->getRepository(Book::class)->find($id);
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
        if($form->isSubmitted()&& $form->isValid()){
            $bookCover = $form->get('picture')->getData();
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
            }
            $manager->persist($book);
            $manager->flush();
            return $this->redirectToRoute('app_single_book', ['id' => $id]);
        }


        return $this->render('edit_book/index.html.twig', [
            'formulaire'=>$form->createView(),
        ]);
    }
}
