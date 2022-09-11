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
            if (isset($_POST['Author']))
                AuthorFactory::createMany(5);
        }
        return $this->render('factory/index.html.twig');
    }
}
