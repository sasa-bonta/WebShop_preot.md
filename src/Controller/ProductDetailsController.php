<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

// http://localhost:8000/product-details
class ProductDetailsController extends AbstractController
{
    /**
     * @Route("/product-details", name="index")
     */
    public function index() {
        return $this->render('product-details/index.html.twig');
    }
}