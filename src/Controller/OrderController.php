<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Form\OrderType;
use App\Repository\CartItemRepository;
use App\Repository\OrderItemRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\SearchCriteria\OrderSearchCriteria;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use DateTimeZone;

/**
 * @Route("/admin/order")
 */
class OrderController extends AbstractController
{
    private array $STATUS = [
        1 => 'in process',
        2 => 'sent',
        3 => 'arrived'
    ];

    /**
     * @Route("/", name="order_index", methods={"GET"})
     */
    public function index(Request $request, OrderRepository $orderRepository): Response
    {
        try {
            $searchOrder = new OrderSearchCriteria($request->query->all());
        } catch (Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        $length = $orderRepository->countTotal($searchOrder);
        if ($searchOrder->getPage() > ceil($length / $searchOrder->getLimit()) && $searchOrder->getPage() > 1) {
            throw new BadRequestHttpException("Page limit exceed");
        }

        return $this->render('admin/order/index.html.twig', [
            'orders' => $orderRepository->search($searchOrder),
            'status' => $this->STATUS,
            'length' => $length,
            'limit' => $searchOrder->getLimit()
        ]);
    }

    /**
     * @Route("/new", name="order_new", methods={"GET","POST"})
     */
    public function new(Request $request, CartItemRepository $cartRepository, ProductRepository $productRepository): Response
    {
        $user = $this->getUser();
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
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

            return $this->redirectToRoute('order_index');
        }

        return $this->render('admin/order/new.html.twig', [
            'order' => $order,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="order_show", methods={"GET"})
     */
    public function show(Order $order): Response
    {
        return $this->render('admin/order/show.html.twig', [
            'order' => $order,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="order_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Order $order): Response
    {
        $form = $this->createForm(OrderType::class, $order);
        $form->remove('status');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('order_index');
        }

        return $this->render('admin/order/edit.html.twig', [
            'order' => $order,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="order_delete", methods={"POST"})
     */
    public function delete(Request $request, Order $order, OrderItemRepository $itemRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $order->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $items = $itemRepository->findBy(['order' => $order->getId()]);
            foreach ($items as $item) {
                $entityManager->remove($item);
            }
            $entityManager->remove($order);
            $entityManager->flush();
        }

        return $this->redirectToRoute('order_index');
    }

    /**
     * @Route("/next/{id}", name="order_next_status", methods={"POST"})
     */
    public function nextStatus(Request $request, Order $order): Response
    {
        if ($this->isCsrfTokenValid('nextStatus' . $order->getId(), $request->request->get('_token'))) {
            $statNum = array_search($order->getStatus(), $this->STATUS);

            if ($statNum < 3) {
                if (!empty($this->STATUS[$statNum + 1])) {
                    $order->setStatus($this->STATUS[$statNum + 1]);
                }
            }
            $this->getDoctrine()->getManager()->flush();

        }

        return $this->redirectToRoute('order_index');
    }

    /**
     * @Route("/prev/{id}", name="order_prev_status", methods={"POST"})
     */
    public function prevStatus(Request $request, Order $order): Response
    {
        if ($this->isCsrfTokenValid('prevStatus' . $order->getId(), $request->request->get('_token'))) {
            $statNum = array_search($order->getStatus(), $this->STATUS);

            if ($statNum > 1) {
                if (!empty($this->STATUS[$statNum - 1])) {
                    $order->setStatus($this->STATUS[$statNum - 1]);
                }
            }
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->redirectToRoute('order_index');
    }
}
