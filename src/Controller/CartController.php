<?php


namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Form\OrderType;
use App\Repository\CartItemRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/cart")
 */
class CartController extends AbstractController
{
    /**
     * @Route("/", name="cart_index", methods={"GET","POST"})
     */
    public function index(Request $request, CartItemRepository $cartRepository, ProductRepository $productRepository): Response
    {
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $total = 0;
            $items = $cartRepository->findBy(['userId' => $this->getUser()->getId()]);
            $order->setUserId($this->getUser()->getId());

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

            return $this->redirectToRoute('cart_index', ['success' => true]);
        }
        return $this->render('main/cart/cart.html.twig', [
            'order' => $order,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/order", name="cart_order", methods={"GET"})
     */
    public function placeOrder(): Response
    {
//        $form = $this->createForm(OrderType::class, $order);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $entityManager = $this->getDoctrine()->getManager();
//            $dateTime = new DateTime(null, new DateTimeZone('Europe/Athens'));
//
//            $order->setCreatedAt($dateTime);
//            $order->setStatus("in process");
//
//            // @todo 10/06/2021 empty card
//
//            $entityManager->persist($order);
//            $entityManager->flush();
//
//            return $this->redirectToRoute('cart_index');
//        }

        return $this->render('main/order/place_order.html.twig');
    }
}