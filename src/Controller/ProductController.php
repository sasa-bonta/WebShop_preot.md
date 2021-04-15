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
 * @Route("/products")
 * @method Exception(string $string)
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/", name="product_index", methods={"GET"})
     * @throws Exception
     */
    public function index(ProductRepository $productRepository): Response
    {
        // http://localhost:8000/product/?name=product&category=tools&page=1&limit=2&order=price
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
        if ($limit <= 0) {
            throw new Exception('Limit must be positive');
        }
        if ($page <= 0) {
            throw new Exception('Number of page must be positive');
        }
        $offset = ($page - 1) * $limit;
        return $this->render('main/product/index.html.twig', [
            'products' => $productRepository->search($name, $category, $orderBy, $limit, $offset),
            'length' => $productRepository->countTotal($name, $category),
            'limit' => $limit
        ]);
    }

    function generateRandomString($length = 10) {
        return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
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
//            $categ = ['cars', 'toys', 'supplies', 'tools'];
//            $product->setName('product' .rand(0, 30));
//            $product->setCode($this->generateRandomString());
//            $product->setCategory($categ[rand(0,3)]);
//            $product->setPrice(rand(1, 1601));
//            $product->setDescription('Lorem ipsum dolor sit amet, consectetuer adipiscing elit. ');
//            $product->setImgPath('assets/main/images/product1.jpg');
            $product->setCreatedAt($dateTime);
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('product_index');
        }

        return $this->render('main/product/new.html.twig', [
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
        return $this->render('main/product/product_details.html.twig', [
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

        return $this->render('main/product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{code}", name="product_delete", methods={"POST"})
     */
    public function delete(Request $request, Product $product): Response
    {
        if ($this->isCsrfTokenValid('delete' . $product->getCode(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('product_index');
    }
}
