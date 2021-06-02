<?php

namespace App\Controller\order;

use App\Entity\PaymentDetails;
use App\Form\PaymentDetailsType;
use App\Repository\PaymentDetailsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/payment/details")
 */
class PaymentDetailsController extends AbstractController
{
    /**
     * @Route("/", name="payment_details_index", methods={"GET"})
     */
    public function index(PaymentDetailsRepository $paymentDetailsRepository): Response
    {
        return $this->render('payment_details/index.html.twig', [
            'payment_details' => $paymentDetailsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="payment_details_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $paymentDetail = new PaymentDetails();
        $form = $this->createForm(PaymentDetailsType::class, $paymentDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($paymentDetail);
            $entityManager->flush();

            return $this->redirectToRoute('payment_details_index');
        }

        return $this->render('payment_details/new.html.twig', [
            'payment_detail' => $paymentDetail,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="payment_details_show", methods={"GET"})
     */
    public function show(PaymentDetails $paymentDetail): Response
    {
        return $this->render('payment_details/show.html.twig', [
            'payment_detail' => $paymentDetail,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="payment_details_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, PaymentDetails $paymentDetail): Response
    {
        $form = $this->createForm(PaymentDetailsType::class, $paymentDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('payment_details_index');
        }

        return $this->render('payment_details/edit.html.twig', [
            'payment_detail' => $paymentDetail,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="payment_details_delete", methods={"POST"})
     */
    public function delete(Request $request, PaymentDetails $paymentDetail): Response
    {
        if ($this->isCsrfTokenValid('delete'.$paymentDetail->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($paymentDetail);
            $entityManager->flush();
        }

        return $this->redirectToRoute('payment_details_index');
    }
}
