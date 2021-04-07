<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

// http://localhost:8000
class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="main")
     */
    public function main() {
        return $this->render('main/index.html.twig');
    }
}