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

            $data = $request->request->all();
            if (isset($data['author'])) {

                if (isset($_POST['edit'])) {
                    $id = $data['author'];
                    return $this->redirectToRoute('author_profile' , ['id' => $id]);
                }
                if (isset($_POST['remove'])) {
                    $authors = $this->getDoctrine()->getRepository(Author::class)->find($data['author']);
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
                $data = $request->request->all();
                $error = $this->castomAuthorValidate($data, $id);
                if (count($error) > 0)
                    return $this->render('author/profile.html.twig', [
                        'author' => $data,
                        'error' => $error,
                    ]);
                $author = $this->save($data, $author);
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
                $data = $request->request->all();
                $author = new Author();
                $error = $this->castomAuthorValidate($data, $author->getId());
                if (count($error) > 0)
                    return $this->render('author/create.html.twig', [
                        'author' => $data,
                        'error' => $error,
                    ]);
                $this->save($data, $author);
            }
            return $this->redirectToRoute('author_show');
        }
        return $this->render('author/create.html.twig');
    }

    public function castomAuthorValidate($data, $id)
    {
        $error = [];
        if ($data['name'] === '')
            $error['name'] = 'Укажите ФИО';
        if ($data['email'] === '')
            $error['email'] = 'Введите почту';
        else{
            $author = $this->getDoctrine()->getRepository(Author::class)->findBy(['email' => $data['email']]);
            if (count($author) && $author[0]->getId() != $id)
                $error['email'] = 'Такая почта уже существует';
        }
        return $error;
    }

    public function save($data, Author $author): Author
    {
        $author->setName($data['name']);
        if (!empty($data['dateOfBirth']))
            $author->setDateOfBirth(\DateTime::createFromFormat('Y-m-d', $data['dateOfBirth']));
        $author->setPhone($data['phone']);
        $author->setEmail($data['email']);

        $doct = $this->getDoctrine()->getManager();
        $doct->persist($author);
        $doct->flush();
        return $author;
    }
}
