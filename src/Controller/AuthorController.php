<?php

namespace App\Controller;

use App\Entity\Author;
use mysql_xdevapi\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthorController extends AbstractController
{
    /**
     * @Route("/author/show", name="author_show")
     */
    public function show(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            if (isset($_POST['back']))
                return $this->redirectToRoute('mainpage');
            if (isset($_POST['create']))
                return $this->redirectToRoute('author_create');

            $date = $request->request->all();
            if (isset($date['author'])) {

                if (isset($_POST['edit'])) {
                    $id = $date['author'];
                    return $this->redirectToRoute('author_profile' , ['id' => $id]);
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
        return $this->render('author/show.html.twig', [
            "authors" => $authors
        ]);
    }
    /**
     * @Route("/author/show/profile/{id}", name="author_profile")
     */
    public function profile($id, Request $request): Response
    {
        $author = $this->getDoctrine()->getRepository(Author::class)->find($id);

        if ($request->isMethod('POST')) {
            if (isset($_POST['back']))
                return $this->redirectToRoute('author_show');
            if (isset($_POST['edit'])){
                $date = $request->request->all();
                $error = $this->castomAuthorValidate($date, $id);
                if (count($error) > 0)
                    return $this->render('author/profile.html.twig', [
                        'author' => $date,
                        'error' => $error,
                    ]);
                $author = $this->save($date, $author);
            }
        }
        return $this->render('author/profile.html.twig', [
            "author" => $author,
        ]);
    }
    /**
     * @Route("/author/create", name="author_create")
     */
    public function create(Request $request): Response
    {
        if ($request->isMethod('POST'))
        {
            if (isset($_POST['create'])){
                $date = $request->request->all();
                $author = new Author();
                $error = $this->castomAuthorValidate($date, $author->getId());
                if (count($error) > 0)
                    return $this->render('author/create.html.twig', [
                        'author' => $date,
                        'error' => $error,
                    ]);
                $this->save($date, $author);
            }
            return $this->redirectToRoute('author_show');
        }
        return $this->render('author/create.html.twig');
    }

    public function castomAuthorValidate($date, $id)
    {
        $error = [];
        if ($date['name'] === '')
            $error['name'] = 'Укажите ФИО';
        if ($date['email'] === '')
            $error['email'] = 'Введите почту';
        else{
            $author = $this->getDoctrine()->getRepository(Author::class)->findBy(['email' => $date['email']]);
            if (count($author) && $author[0]->getId() != $id)
                $error['email'] = 'Такая почта уже существует';
        }
        return $error;
    }

    public function save($date, Author $author): Author
    {
        $author->setName($date['name']);
        if (!empty($date['dateOfBirth']))
            $author->setDateOfBirth(\DateTime::createFromFormat('Y-m-d', $date['dateOfBirth']));
        $author->setPhone($date['phone']);
        $author->setEmail($date['email']);

        $doct = $this->getDoctrine()->getManager();
        $doct->persist($author);
        $doct->flush();
        return $author;
    }
}
