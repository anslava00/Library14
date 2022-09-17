<?php

namespace App\Controller;


use App\Entity\Book;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RequestsController extends AbstractController
{
    /**
     * @Route("/requests", name="requests")
     */
    public function show(Request $request): Response
    {
        $books = [];
        if ($request->isMethod('POST')) {
            if (isset($_POST['back']))
                return $this->redirectToRoute('mainpage');
            if (isset($_POST['ORM']))
            {
                $books = $this->getDoctrine()->getRepository(Book::class)->findOrherRequestORM();
            }
            elseif (isset($_POST['SQL']))
            {
                $books = $this->getDoctrine()->getRepository(Book::class)->findOrherRequestSQL();
            }
            elseif (isset($_POST['ALL']))
            {
                $books = $this->getDoctrine()->getRepository(Book::class)->findAll();
            }
        }
        return $this->render('requests/show.html.twig', [
            'books' => $books,
        ]);
    }
}
