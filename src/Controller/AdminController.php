<?php


namespace App\Controller;


use App\Entity\Product;
use App\Entity\User;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use DateTime;
use DateTimeZone;
use Exception;
use App\SearchCriteria;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin/products")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="product_list", methods={"GET"})
     */
    public function list(ProductRepository $productRepository, Request $request): Response
    {
        // http://localhost:8000/admin/products/

       $name = $request->query->get('name');
        $category = $request->query->get('category');
        $page = $request->query->get('page', 1);
        $limit = $request->query->get('limit', 16);
        if ($limit > 120) {
            throw new BadRequestHttpException("400");
        }
        $orderBy = $request->query->get('order', 'created_at:ASC');
        $arr = explode(":", $orderBy, 2);
        $order = $arr[0];
        $ascDesc = $arr[1];

        try {
            $searchCriteria = new SearchCriteria($name, $category, $page, $limit, $order, $ascDesc);
        } catch (Exception $e) {
            throw new BadRequestHttpException("400");
        }

        $length = $productRepository->countTotal($searchCriteria);
        if ($page > ceil($length / $limit)) {
            throw new BadRequestHttpException("400");
        }

        return $this->render('admin/product/list_of_products.html.twig', [
            'products' => $productRepository->search($searchCriteria),
            'length' => $length,
            'limit' => $searchCriteria->getLimit()
        ]);
    }

    function generateRandomString($length = 10)
    {
        return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
    }

    /**
     * @Route("/new", name="product_new", methods={"GET","POST"})
     */
    public function new(Request $request, ProductRepository $repo): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            # Errors existent Nickname and/or Email
            $errors = [];
            $repo = $this->getDoctrine()->getRepository(Product::class);
            if ($repo->count(['code' => $product->getCode()]) > 0) {
                array_push($errors, "This code already exists");
            }
            if (!empty($errors)) {
                return $this->render('admin/product/new.html.twig', [
                    'errors' => $errors,
                    'product' => $product,
                    'form' => $form->createView(),
                ]);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $dateTime = new DateTime(null, new DateTimeZone('Europe/Athens'));
            # Random product generator. To make it work comment all the fields from ProductType
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

            return $this->redirectToRoute('product_list');
        }

        return $this->render('admin/product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{code}", name="product_show_detailed", methods={"GET"})
     */
    public function show(Product $product): Response
    {
        return $this->render('admin/product/show.html.twig', [
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

            # Errors existent Nickname and/or Email
            $errors = [];
            $repo = $this->getDoctrine()->getRepository(Product::class);
            if ($repo->count(['code' => $product->getCode()]) > 1) {
                array_push($errors, "This code already exists");
            }
            if (!empty($errors)) {
                return $this->render('admin/product/edit.html.twig', [
                    'errors' => $errors,
                    'product' => $product,
                    'form' => $form->createView(),
                ]);
            }

            $dateTime = new DateTime(null, new DateTimeZone('Europe/Athens'));
            $product->setUpdatedAt($dateTime);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('product_list');
        }

        return $this->render('admin/product/edit.html.twig', [
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
        return $this->redirectToRoute('product_list');
    }
}