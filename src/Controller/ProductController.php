<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\SearchCriteria\ProductSearchCriteria;
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
     */
    public function index(ProductRepository $productRepository, Request $request): Response
    {
        try {
            $searchCriteria = new ProductSearchCriteria($request->query->all());
        } catch (Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        $length = $productRepository->countTotal($searchCriteria);
        if ($searchCriteria->getPage() > ceil($length / $searchCriteria->getLimit()) && $searchCriteria->getPage() > 1) {
            throw new BadRequestHttpException("Page limit exceed");
        }

        $products = $productRepository->search($searchCriteria);
        foreach ($products as $product) {
            $product->writeImgPathEgal($product->readImgPathsArray());
        }

        return $this->render('main/product/index.html.twig', [
            'products' => $products,
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
        $product->writeImgPathEgal($product->readImgPathsArray());
        return $this->render('main/product/product_details.html.twig', [
            'product' => $product,
        ]);
    }
}
