<?php

namespace App\Controller\order;

use App\Entity\CreditCardDetails;
use App\Form\CreditCardDetailsType;
use App\Repository\CreditCardDetailsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/credit/card/details")
 */
class CreditCardDetailsController extends AbstractController
{
    /**
     * @Route("/", name="credit_card_details_index", methods={"GET"})
     */
    public function index(CreditCardDetailsRepository $creditCardDetailsRepository): Response
    {
        return $this->render('credit_card_details/index.html.twig', [
            'credit_card_details' => $creditCardDetailsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="credit_card_details_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $creditCardDetail = new CreditCardDetails();
        $form = $this->createForm(CreditCardDetailsType::class, $creditCardDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($creditCardDetail);
            $entityManager->flush();

            return $this->redirectToRoute('credit_card_details_index');
        }

        return $this->render('credit_card_details/new.html.twig', [
            'credit_card_detail' => $creditCardDetail,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="credit_card_details_show", methods={"GET"})
     */
    public function show(CreditCardDetails $creditCardDetail): Response
    {
        return $this->render('credit_card_details/show.html.twig', [
            'credit_card_detail' => $creditCardDetail,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="credit_card_details_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, CreditCardDetails $creditCardDetail): Response
    {
        $form = $this->createForm(CreditCardDetailsType::class, $creditCardDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('credit_card_details_index');
        }

        return $this->render('credit_card_details/edit.html.twig', [
            'credit_card_detail' => $creditCardDetail,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="credit_card_details_delete", methods={"POST"})
     */
    public function delete(Request $request, CreditCardDetails $creditCardDetail): Response
    {
        if ($this->isCsrfTokenValid('delete'.$creditCardDetail->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($creditCardDetail);
            $entityManager->flush();
        }

        return $this->redirectToRoute('credit_card_details_index');
    }
}
