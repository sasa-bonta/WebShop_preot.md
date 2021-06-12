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
 * @Route("/cart")
 */
class CartController extends AbstractController
{
    /**
     * @Route("/", name="cart_index", methods={"GET","POST"})
     */
    public function index(Request $request, CartItemRepository $cartRepository, ProductRepository $productRepository): Response
    {
        $user = $this->getUser();
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $dateTime = new DateTime(null, new DateTimeZone('Europe/Athens'));
            $total = 0;
            $errors = null;

            $order->setCreatedAt($dateTime);
            $order->setUserId($user->getId());
            $order->setStatus("in process");
            $items = $cartRepository->findBy(['userId' => $user->getId()]);
            if (empty($items)) {
                return $this->redirectToRoute('cart_index');
            }

            // verifying card's expiration date
            if ($order->getPayment()->getCard()->getExpiresAt() !== null) {
                $expiresAt = new DateTime($order->getPayment()->getCard()->getExpiresAt()->format('Y-m-1 H:i:s.v'));
                $now = new DateTime();
                if ($expiresAt <= $now) {
                    $errors['expiresAt'] = "The credit card with indicated date is already expired";
                }
                if ($expiresAt > $now->modify('+10 year')) {
                    $errors['expiresAt'] = "Card with indicated expiration date cannot exist";
                }
                if (isset($errors)) {
                    return $this->render('main/cart/cart.html.twig', [
                        'order' => $order,
                        'form' => $form->createView(),
                        'errors' => $errors
                    ]);
                }
            }

            foreach ($items as $item) {
                $product = $productRepository->findOneBy(['code' => $item->getCode()]);
                $orderItem = new OrderItem();
                $orderItem->setProductCode($item->getCode());
                $orderItem->setPrice($product->getPrice());
                $orderItem->setAmount($item->getAmount());
                $order->addItem($orderItem);
                $total += $orderItem->getPrice() * $orderItem->getAmount();
                $product->setAvailableAmount($product->getAvailableAmount() - $orderItem->getAmount());
                $entityManager->remove($item);
                $entityManager->flush();
            }

            $order->setTotal($total);
            $entityManager->persist($order);
            $entityManager->flush();

            return $this->redirectToRoute('cart_index', ['success' => true]);
        }
        return $this->render('main/cart/cart.html.twig', [
            'order' => $order,
            'form' => $form->createView()
        ]);
    }
}