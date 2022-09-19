<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Factory\AuthorFactory;
use App\Factory\BookFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FactoryController extends AbstractController
{
    /**
     * @Route("index.php/factory", name="factory_show")
     */
    public function index(Request $request): Response
    {
        if ($request->isMethod('POST'))
        {
            if (isset($_POST['back']))
                return $this->redirectToRoute('mainpage');
            $default = 5;
            if ($request->request->all()['count'])
                $default = $request->request->all()['count'];
            if (isset($_POST['author']))
                $this->createAuthor($default);
            if (isset($_POST['book']))
                $this->createBook($default);
            if (isset($_POST['relation']))
                $this->createRelation();
        }
        return $this->render('factory/index.html.twig');
    }

    public function createAuthor($default):void
    {
        echo 'some';
//        AuthorFactory::createMany($default);
    }
    public function createBook($default):void
    {
        BookFactory::createMany($default);
    }
    public function createRelation():void
    {
        $books = $this->getDoctrine()->getRepository(Book::class)->findAll();
        $authors = $this->getDoctrine()->getRepository(Author::class)->findAll();
        $countAuthors = count($authors);
        foreach ($books as $book)
            {
                $authorsBook = $book->getAuthors();
                if (count($authorsBook) != 0)
                    continue;
                $requireAuthor = rand(1, 5);
                $iAuthors = $this->randUnic($countAuthors, $requireAuthor);
                for ($i = 0; $i < $requireAuthor; $i++)
                {
                    $book->addAuthor($authors[$iAuthors[$i]]);
                    $authors[$iAuthors[$i]]->setCountBook(1);
                }
                $doct = $this->getDoctrine()->getManager();
                $doct->persist($book);
                $doct->flush();
            }
    }
    public function randUnic($countAuthors, $requireAuthor):array
    {
        $iAuthors = [];
        for ($i = 0; $i < $requireAuthor; $i++)
        {
            $newI = 0;
            while (true){
                $newI = rand(0, $countAuthors - 1);
                $F = true;
                foreach ($iAuthors as $iAuthor)
                {
                    if ($iAuthor == $newI){
                        $F = false;
                        break;
                    }
                }
                if ($F)
                    break;

            }
            $iAuthors[] = $newI;
        }
        return $iAuthors;
    }
}
