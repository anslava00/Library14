<?php

namespace App\Controller;

use App\Factory\AuthorFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FactoryController extends AbstractController
{
    /**
     * @Route("/factory", name="factory_show")
     */
    public function index(Request $request): Response
    {
        if ($request->isMethod('POST'))
        {
            if (isset($_POST['back']))
                return $this->redirectToRoute('mainpage');
            if (isset($_POST['author']))
                $this->createAuthor($request->request->all()['count']);
            if (isset($_POST['book']))
                $this->createBook($request->request->all()['count']);

        }
        return $this->render('factory/index.html.twig');
    }

    public function createAuthor($default = 5):void
    {
        AuthorFactory::createMany($default);
    }
    public function createBook($default = 5):void
    {

    }
}
