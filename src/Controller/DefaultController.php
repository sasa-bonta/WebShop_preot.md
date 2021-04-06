<?php


namespace App\Controller;

// http://localhost:8000/products

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index() {
        return $this->render('main/index.html.twig');
    }
}