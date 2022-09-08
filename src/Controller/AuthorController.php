<?php

namespace App\Controller;

use App\Entity\Author;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthorController extends AbstractController
{
    /**
     * @Route("/author/show", name="app_author")
     */
    public function show(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            if (isset($_POST['back']))
                return $this->redirect('/main_page');
            if (isset($_POST['create']))
                return $this->redirect('/author/create');

            $date = $request->request->all();
            if (isset($date['author'])) {

                if (isset($_POST['edit'])) {
                    $id = $date['author'];
                    return $this->redirect('/author/show/profile/' . (int)$id);
                }
                if (isset($_POST['remove'])) {
                    $authors = $this->getDoctrine()->getRepository(Author::class)->find($date['author']);
                    if (isset($authors)){
                        $doct = $this->getDoctrine()->getManager();
                        $doct->remove($authors);
                        $doct->flush();
                    }

                }
            }
        }

        $authors = $this->getDoctrine()->getRepository(Author::class)->findAll();
        return $this->render('author/index.html.twig', [
            "authors" => $authors
        ]);
    }
    /**
     * @Route("/author/show/profile/{id}", name="app_author_profile")
     */
    public function profile($id, Request $request): Response
    {
        $author = $this->getDoctrine()->getRepository(Author::class)->find($id);

        if ($request->isMethod('POST')) {
            if (isset($_POST['back']))
                return $this->redirect('/author/show');
            if (isset($_POST['edit'])){
                $date = $request->request->all();
                $author = $this->save($date, $author);
            }
        }
        return $this->render('author/profile.html.twig', [
            "author" => $author,
        ]);
    }
    /**
     * @Route("/author/create", name="app_create_author")
     */
    public function create(Request $request): Response
    {
        if ($request->isMethod('POST'))
        {
            if (isset($_POST['create'])){
                $date = $request->request->all();
                $author = new Author();
                $this->save($date, $author);

                return $this->redirect('/author/show');
            }
            if (isset($_POST['back']))
                return $this->redirect('/author/show');
        }
        return $this->render('author/create.html.twig');
    }

    public function save($date, Author $author): Author
    {
        $author->setName($date['name']);
        $author->setDateOfBirth(\DateTime::createFromFormat('Y-m-d', $date['date']));
        $author->setPhone($date['phone']);
        $author->setEmail($date['email']);

        $doct = $this->getDoctrine()->getManager();
        $doct->persist($author);
        $doct->flush();
        return $author;
    }
}
