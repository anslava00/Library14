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
    public function index(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            if (isset($_POST['back']))
                return $this->redirectToRoute('mainpage');
            if (isset($_POST['create']))
                return $this->redirectToRoute('book_create');
            $date = $request->request->all();
            if (isset($date['book'])) {
                if (isset($_POST['edit'])) {
                    $id = $date['book'];
                    return $this->redirectToRoute('book_details' , ['id' => $id]);
                }
                if (isset($_POST['remove'])) {
                    $book = $this->getDoctrine()->getRepository(Book::class)->find($date['book']);
                    if (isset($book)){
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

        if ($request->isMethod('POST')) {
            if (isset($_POST['back']))
                return $this->redirectToRoute('book_show');
            if (isset($_POST['edit'])){
                $file = $request->files->get('image');
                $originalNameFile = '';
                if (isset($file)){
                    $filesystem = new Filesystem();
                    $filesystem->remove('image/'.$book->getImage());
                    $path = $this->getParameter('kernel.project_dir').'/public/image';
                    $originalNameFile = $file->getClientOriginalName();
                    $file->move($path, $originalNameFile);
                }

                $date = $request->request->all();
                $authors = $book->getAuthors();
                foreach ($authors as $author){
                    $haveInArray = false;
                    foreach(range(0, $date['countAuthor'] - 1) as $i) {
                        if ($author->getId() == $date['author' . $i]){
                            $haveInArray = true;
                            break;
                        }
                    }
                    if (!$haveInArray){
                        $book->removeAuthor($author);
                    }
                }
                foreach(range(0, $date['countAuthor'] - 1) as $i) {
                    $author = $this->getDoctrine()->getRepository(Author::class)->find($date['author' . $i]);
                    $book->addAuthor($author);
                }
                $this->save($date, $book, $originalNameFile);
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
            if (isset($_POST['create'])){
                $file = $request->files->get('image');
                $originalNameFile = '';
                if (isset($file)){
                    $path = $this->getParameter('kernel.project_dir').'/public/image';
                    $originalNameFile = $file->getClientOriginalName();

                    $file->move($path, $originalNameFile);
                }

                $date = $request->request->all();
                $book = new Book();
                foreach(range(0, $date['countAuthor'] - 1) as $i) {
                    $author = $this->getDoctrine()->getRepository(Author::class)->find($date['author' . $i]);
                    $book->addAuthor($author);
                }
                $this->save($date, $book, $originalNameFile);
            }
            return $this->redirectToRoute('book_show');
        }
        $authors = $this->getDoctrine()->getRepository(Author::class)->findAll();
        return $this->render('book/create.html.twig', [
            "authors" => $authors
        ]);
    }

    public function save($date, Book $book, $fileName): Book
    {
        $book->setTitle($date['title']);
        $book->setDescription($date['description']);
        if ($fileName != '')
            $book->setImage($fileName);
        $book->setYear(\DateTime::createFromFormat('Y-m-d', $date['year']));
        $doct = $this->getDoctrine()->getManager();
        $doct->persist($book);
        $doct->flush();
        return $book;
    }
}
