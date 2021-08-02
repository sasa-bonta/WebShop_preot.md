<?php


namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Form\OrderType;
use App\Repository\CartItemRepository;
use App\Repository\ProductRepository;
use DateTime;
use DateTimeZone;
use Stripe\Checkout\Session;
use Stripe\Stripe;
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
            $stripeItems = [];

            $order->setCreatedAt($dateTime);
            $order->setUserId($user->getId());
            $order->setStatus("in process");
            $items = $cartRepository->findBy(['userId' => $user->getId()]);
            if (empty($items)) {
                return $this->redirectToRoute('cart_index');
            }

            foreach ($items as $item) {
                $product = $productRepository->findOneBy(['code' => $item->getCode()]);
                $orderItem = new OrderItem();
                $orderItem->setProductCode($item->getCode());
                $orderItem->setPrice($product->getPrice());
                $orderItem->setAmount($item->getAmount());
                /**
                 * create array of order items
                 * ['price' => $price, 'quantity' => $quantity]
                 */
                $itemArray['price'] = $product->getStripePriceId();
                $itemArray['quantity'] = $orderItem->getAmount();
                $stripeItems[] = $itemArray;
                $order->addItem($orderItem);
                $total += $orderItem->getPrice() * $orderItem->getAmount();
                $product->setAvailableAmount($product->getAvailableAmount() - $orderItem->getAmount());
                $entityManager->remove($item);
                $entityManager->flush();
            }

            // @fixme 01/08/2021 bug here
            $entityManager->flush();
            $order->setTotal($total);
            $entityManager->persist($order);

            /**
             * Stripe checkout session
             */
            if ($order->getPayment()->getType() === 'card') {
                // creating stripe checkout session
                Stripe::setApiKey($this->getParameter('stripe_secret_key'));
                $checkout_session = Session::create([
                    'success_url' => $this->getParameter('domain_url') . '/cart/?success=1',
                    // @todo 01/08/2021 add cancel page
                    'cancel_url' => $this->getParameter('domain_url') . '/canceled.html',
                    'payment_method_types' => ['card'],
                    'mode' => 'payment',
                    'line_items' => $stripeItems,
                ]);

                header("HTTP/1.1 303 See Other");
                header("Location: " . $checkout_session->url);
            }
        }
        return $this->render('main/cart/cart.html.twig', [
            'order' => $order,
            'form' => $form->createView()
        ]);
    }
}