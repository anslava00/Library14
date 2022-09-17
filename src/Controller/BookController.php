<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    /**
     * @Route("/book/show", name="book_show")
     */
    public function show(Request $request): Response
    {
        if ($request->isMethod('POST'))
        {
            if (isset($_POST['sorted']))
            {
                $data = $request->request->all();
                $filterSort = [
                    'sort' => $data['typeSort'],
                    'titleFilter' => $data['filterTitle'],
                    'dateStart' => $data['dateStart'],
                    'dateEnd' => $data['dateEnd'],
                    'countAuthorStart' => $data['countAuthorStart'],
                    'countAuthorEnd' => $data['countAuthorEnd'],
                    'imageFilter' => $data['imageFilter'],
                ];
                $books = $this->getDoctrine()->getRepository(Book::class)->findAndSort($filterSort);
                return $this->render('book/show.html.twig', [
                    "books" => $books,
                    'filterSort' => $filterSort,
                ]);
            }
            if (isset($_POST['back']))
                return $this->redirectToRoute('mainpage');
            if (isset($_POST['create']))
                return $this->redirectToRoute('book_create');
            $data = $request->request->all();
            if (isset($data['book']))
            {
                if (isset($_POST['edit']))
                {
                    $id = $data['book'];
                    return $this->redirectToRoute('book_details' , ['id' => $id]);
                }
                if (isset($_POST['remove']))
                {
                    $book = $this->getDoctrine()->getRepository(Book::class)->find($data['book']);
                    if (isset($book))
                    {
                        $authors = $book->getAuthors();
                        foreach ($authors as $author)
                        {
                            $author->setCountBook(-1);
                        }
                        $filesystem = new Filesystem();
                        $filesystem->remove('image/'.$book->getImage());
                        $doct = $this->getDoctrine()->getManager();
                        $doct->remove($book);
                        $doct->flush();
                    }
                }
            }
        }

        $books = $this->getDoctrine()->getRepository(Book::class)->findAll();
        return $this->render('book/show.html.twig', [
            "books" => $books,
            ]);
    }
    /**
     * @Route("/book/show/details/{id}", name="book_details")
     */
    public function details($id, Request $request): Response
    {
        $book = $this->getDoctrine()->getRepository(Book::class)->find($id);

        if ($request->isMethod('POST'))
        {
            if (isset($_POST['back']))
                return $this->redirectToRoute('book_show');
            if (isset($_POST['edit']))
            {
                $book->setFile($request->files->get('image'));
                $data = $request->request->all();
                $authors = $book->getAuthors();
                foreach ($authors as $author)
                {
                    $haveInArray = false;
                    for ($i = 0; $i < $data['countAuthor']; $i++)
                    {
                        if ($author->getId() == $data['author' . $i])
                        {
                            $haveInArray = true;
                            break;
                        }
                    }
                    if (!$haveInArray)
                    {
                        $book->removeAuthor($author);
                        $author->setCountBook(-1);
                    }
                }
                for ($i = 0; $i < $data['countAuthor']; $i++)
                {
                    $author = $this->getDoctrine()->getRepository(Author::class)->find($data['author' . $i]);
                    $book->addAuthor($author);
                    $author->setCountBook(1);
                }
                $this->save($data, $book);
            }
        }
        $authors = $this->getDoctrine()->getRepository(Author::class)->findAllExcept($book->getAuthors());
        return $this->render('book/details.html.twig', [
            "book" => $book,
            "chooseAuthors" => $book->getAuthors(),
            'authors' => $authors,
        ]);
    }
    /**
     * @Route("/book/create", name="book_create")
     */
    public function create(Request $request): Response
    {
        if ($request->isMethod('POST'))
        {
            if (isset($_POST['create']))
            {
                $book = new Book();
                $book->setFile($request->files->get('image'));
                $data = $request->request->all();
                for ($i = 0; $i < $data['countAuthor']; $i++)
                {
                    $author = $this->getDoctrine()->getRepository(Author::class)->find($data['author' . $i]);
                    $book->addAuthor($author);
                    $author->setCountBook(1);
                }
                $this->save($data, $book);
            }
            return $this->redirectToRoute('book_show');
        }
        $authors = $this->getDoctrine()->getRepository(Author::class)->findAll();
        return $this->render('book/create.html.twig', [
            "authors" => $authors
        ]);
    }

    public function save($data, Book $book): Book
    {
        $book->setTitle($data['title']);
        if (!empty($data['year']))
            $book->setYear(\DateTime::createFromFormat('Y-m-d', $data['year']));
        $doct = $this->getDoctrine()->getManager();
        $doct->persist($book);
        $doct->flush();
        return $book;
    }
}
