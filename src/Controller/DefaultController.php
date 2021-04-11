<?php


namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="main_page", methods={"GET"})
     */
    public function index(ProductRepository $productRepository): Response
    {
        $name = null;
        $category = null;
        $page = 1;
        $limit = 10;
        $orderBy = 'created_at:ASC';

        if (isset($_GET['name'])) {
            $name = $_GET['name'];
        }
        if (isset($_GET['category'])) {
            $category = $_GET['category'];
        }
        if (isset($_GET['page'])) {
            $page = $_GET['page'];
        }
        if (isset($_GET['limit'])) {
            $limit = $_GET['limit'];
        }
        if (isset($_GET['order'])) {
            $orderBy = $_GET['order'];
        }
        $offset = ($page - 1) * $limit;
        return $this->render('main/index.html.twig', [
            'products' => $productRepository->findByNameCat($name, $category, $orderBy, $limit, $offset),
        ]);
    }
}