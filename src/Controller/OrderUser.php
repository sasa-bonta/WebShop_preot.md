<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Form\OrderType;
use App\Repository\CartItemRepository;
use App\Repository\ProductRepository;
use DateTime;
use DateTimeZone;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/order/item")
 */
class OrderUser extends AbstractController
{
    /**
     * @Route("/new", name="new_order_user", methods={"GET","POST"})
     */
    public function new(Request $request, CartItemRepository $cartRepository, ProductRepository $productRepository): Response
    {
        $user = $this->getUser();
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->remove('status');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $dateTime = new DateTime(null, new DateTimeZone('Europe/Athens'));
            $total = 0;

            $order->setCreatedAt($dateTime);
            $order->setUserId($user->getId());
            $order->setStatus("in process");
            $items = $cartRepository->findBy(['userId' => $user->getId()]);
            foreach ($items as $item) {
                $code = $item->getCode();
                $product = $productRepository->findOneBy(['code' => $code]);
                $orderItem = new OrderItem();
                $orderItem->setProductCode($code);
                $orderItem->setPrice($product->getPrice());
                $orderItem->setAmount($item->getAmount());

                $em = $this->getDoctrine()->getManager();
                $em->flush();

                $order->addItem($orderItem);
                $total += $orderItem->getPrice() * $orderItem->getAmount();
            }
            $order->setTotal($total);
            $entityManager->persist($order);
            $entityManager->flush();

            return $this->redirectToRoute('cart_index');
        }
        return $this->render('main/order/new_order.html.twig', [
            'order' => $order,
            'form' => $form->createView(),
        ]);
    }
}
