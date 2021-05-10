<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\SearchCriteria;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/products")
 * @method Exception(string $string)
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/", name="product_index", methods={"GET"})
     * @throws Exception
     */
    public function index(ProductRepository $productRepository, Request $request): Response
    {
        // http://localhost:8000/products/?name=product&category=tools&page=1&limit=2&order=price

        $name = $request->query->get('name');
        $category = $request->query->get('category');
        $page = $request->query->get('page', 1);
        $limit = $request->query->get('limit', 16);
        if(!in_array($limit, [16, 32, 64, 128])){
            throw new BadRequestHttpException("400");
        }
        $orderBy = $request->query->get('order', 'created_at:ASC');
        $arr = explode(":", $orderBy, 2);
        $order = $arr[0];
        $ascDesc = $arr[1];

        try {
            $searchCriteria = new SearchCriteria($name, $category, $page, $limit, $order, $ascDesc);
        } catch (Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        $length = $productRepository->countTotal($searchCriteria);
        if ($page > ceil($length / $limit) && $page > 1) {
            throw new BadRequestHttpException("Page limit exceed");
        }

        return $this->render('main/product/index.html.twig', [
            'products' => $productRepository->search($searchCriteria),
            'categories' => $productRepository->getCategories(),
            'length' => $length,
            'limit' => $searchCriteria->getLimit()
        ]);
    }

    /**
     * @Route("/{code}", name="product_show", methods={"GET"})
     */
    public function show(Product $product): Response
    {
        return $this->render('main/product/product_details.html.twig', [
            'product' => $product,
        ]);
    }
}
