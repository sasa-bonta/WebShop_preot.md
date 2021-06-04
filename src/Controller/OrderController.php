<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\OrderType;
use App\Repository\CartItemRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use DateTimeZone;

/**
 * @Route("/order")
 */
class OrderController extends AbstractController
{
    /**
     * @Route("/", name="order_index", methods={"GET"})
     */
    public function index(OrderRepository $orderRepository): Response
    {
        return $this->render('order/index.html.twig', [
            'orders' => $orderRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="order_new", methods={"GET","POST"})
     */
    public function new(Request $request, CartItemRepository $cartRepository, ProductRepository $productRepository): Response
    {
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->remove('status');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $dateTime = new DateTime(null, new DateTimeZone('Europe/Athens'));
            $userId = $this->getUserId();
            $total = 0;

            $order->setCreatedAt($dateTime);
            $order->setUserId($userId);
            $order->setStatus("in process");
            $items = $cartRepository->findBy(['userId' => $userId]);
            foreach ($items as $item) {
                $product = $productRepository->findOneBy(['code' => $item->getCode()]);
                $price = $product->getPrice() * $item->getAmount();
                $total += $price;
            }
            $order->setTotal($total);
            $entityManager->persist($order);
            $entityManager->flush();

            return $this->redirectToRoute('order_index');
        }

        return $this->render('order/new.html.twig', [
            'order' => $order,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="order_show", methods={"GET"})
     */
    public function show(Order $order): Response
    {
        return $this->render('order/show.html.twig', [
            'order' => $order,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="order_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Order $order): Response
    {
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('order_index');
        }

        return $this->render('order/edit.html.twig', [
            'order' => $order,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="order_delete", methods={"POST"})
     */
    public function delete(Request $request, Order $order): Response
    {
        if ($this->isCsrfTokenValid('delete' . $order->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($order);
            $entityManager->flush();
        }

        return $this->redirectToRoute('order_index');
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->get('security.token_storage')->getToken()->getUser()->getId();
    }
}
