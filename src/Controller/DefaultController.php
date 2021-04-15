<?php


namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
        $limit = 16;
        $orderBy = 'created_at:ASC';
        $request = Request::createFromGlobals();
        $request->getPathInfo();

        if ($request->query->get('name')) {
            $name = $request->query->get('name');
        }
        if ($request->query->get('category')) {
            $category = $request->query->get('category');
        }
        if ($request->query->get('page')) {
            $page = $request->query->get('page');
        }
        if ($request->query->get('limit')) {
            $limit = $request->query->get('limit');
        }
        if ($request->query->get('order')) {
            $orderBy = $request->query->get('order');
        }
        # Catch and display errors
//        if ($limit <= 0) {
//            throw new Exception('Limit must be positive');
//        }
//        if ($page <= 0) {
//            throw new Exception('Number of page must be positive');
//        }
        $offset = ($page - 1) * $limit;
        return $this->render('main/product/index.html.twig', [
            'products' => $productRepository->search($name, $category, $orderBy, $limit, $offset),
            'length' => $productRepository->countTotal($name, $category),
            'limit' => $limit
        ]);
    }
}