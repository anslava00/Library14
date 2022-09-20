<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainPageController extends AbstractController
{
    /**
     * @Route("/", name="mainpage")
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
            if (isset($_POST['Request']))
                return $this->redirectToRoute('requests');
            if (isset($_POST['Admin']))
                return $this->redirect('admin/dashboard');
            if (isset($_POST['Login']))
                return $this->redirect('/login');
            if (isset($_POST['Register']))
                return $this->redirect('/register');
        }

        return $this->render('main_page/show.html.twig');
    }
}
