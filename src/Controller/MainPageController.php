<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainPageController extends AbstractController
{
    /**
     * @Route("/main_page", name="mainpage")
     */
    public function index(Request $request): Response
    {
        if ($request->isMethod('POST'))
        {
            if (isset($_POST['Author']))
                return $this->redirectToRoute('author_show');
            if (isset($_POST['Factory']))
                return $this->redirectToRoute('factory_show');
            if (isset($_POST['Book']))
                return $this->redirectToRoute('book_show');
        }

        return $this->render('main_page/show.html.twig');
    }
}
