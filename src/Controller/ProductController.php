<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use DateTime;
use DateTimeZone;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/product")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/", name="product_index", methods={"GET"})
     */
    public function index(ProductRepository $productRepository): Response
    {
        // http://localhost:8000/product/?name=product&category=tools&page=1&limit=2&order=price
        $name = null;
        $category = null;
        $page = 1;
        $limit = 10;
        $orderBy = 'created_at';

        if(isset($_GET['name'])) {
            $name = $_GET['name'];
        }
        if(isset($_GET['category'])) {
            $category =  $_GET['category'];
        }
        if(isset($_GET['page'])) {
            $page = $_GET['page'];
        }
        if(isset($_GET['limit'])) {
            $limit = $_GET['limit'];
        }
        if (isset($_GET['order'])) {
            $orderBy = $_GET['order'];
        }
        $offset = ($page - 1) * $limit + 1;
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findByNameCat($name, $category, $orderBy, $limit, $offset),
        ]);
    }

    /**
     * @Route("/new", name="product_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            # Errors solve later
//            $repo = $this->getDoctrine()->getRepository(Product::class);
//            if ($repo->count(['code'=>$product->getCode()]) > 0) {
//                # code 400, display alert. Return this
//                return $this->render('product/new.html.twig', [
//                    'errors' => ['duplicated code'],
//                    'product' => $product,
//                    'form' => $form->createView(),
//                ]);
//            }
            $entityManager = $this->getDoctrine()->getManager();
            try {
                $dateTime = new DateTime(null, new DateTimeZone('Europe/Athens'));
            } catch (Exception $e) {
            }
            # Random product generator. To make it work comment all the fields excluding code from ProductType
//            $categ = ['cars', 'bikes', 'toys', 'notebooks', 'tools'];
//            $product->setName('product' .rand(0, 30));
//            $product->setCategory($categ[rand(0,4)]);
//            $product->setPrice(rand(1, 1601));
//            $product->setDescription('Lorem ipsum dolor sit amet, consectetuer adipiscing elit. ');
//            $product->setImgPath('assets/main/images/product1.jpg');
            $product->setCreatedAt($dateTime);
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{code}", name="product_show", methods={"GET"})
     */
    public function show(Product $product): Response
    {
//        return $this->render('product/show.html.twig', [
//            'product' => $product,
//        ]);
        return $this->render('product/product_details.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/{code}/edit", name="product_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Product $product): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $dateTime = new DateTime(null, new DateTimeZone('Europe/Athens'));
            } catch (Exception $e) {
            }
            $product->setUpdatedAt($dateTime);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{code}", name="product_delete", methods={"POST"})
     */
    public function delete(Request $request, Product $product): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getCode(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('product_index');
    }
}
