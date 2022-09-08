<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainPageController extends AbstractController
{
    /**
     * @Route("/main_page", name="app_main_page")
     */
    public function index(Request $request): Response
    {
        if ($request->isMethod('POST'))
        {
            if (isset($_POST['Author']))
                return $this->redirect('/author/show');
            if (isset($_POST['Factory']))
                return $this->redirect('/factory');
        }

        return $this->render('main_page/index.html.twig');
    }
}
