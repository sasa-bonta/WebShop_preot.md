<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\SearchCriteria\UserSearchCriteria;
use App\Service\CheckUserService;
use Exception;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


/**
 * @Route("/admin/users")
 */
class UserController extends AbstractController
{
    private CheckUserService $checkUser;

    /**
     * UserController constructor.
     */
    public function __construct(CheckUserService $checkUser)
    {
        $this->checkUser = $checkUser;
    }

    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(Request $request, UserRepository $userRepository): Response
    {
        try {
            $searchUser = new UserSearchCriteria($request->query->all());
        } catch (Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        $length = $userRepository->countTotal($searchUser);
        if ($searchUser->getPage() > ceil($length / $searchUser->getLimit()) && $searchUser->getPage() > 1) {
            throw new BadRequestHttpException("Page limit exceed");
        }

        return $this->render('admin/user/index.html.twig', [
            'users' => $userRepository->search($searchUser),
            'length' => $length,
            'limit' => $searchUser->getLimit()
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && !$form->isValid()) {
            $errors = $this->checkUser->checkData($user);
            if (!empty($errors)) {
                return $this->render('admin/user/new.html.twig', [
                    'errors' => $errors,
                    'user' => $user,
                    'form' => $form->createView(),
                ]);
            }
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            if (empty($plainPassword)) {
                $errors['pass'] = "The password must not be empty";
            }
            $errors = $this->checkUser->checkData($user);
            if (!empty($errors)) {
                return $this->render('admin/user/new.html.twig', [
                    'errors' => $errors,
                    'user' => $user,
                    'form' => $form->createView(),
                ]);
            }
            $user->setActivated(true);
            $user->setPassword($encoder->encodePassword($user, $form->get('plainPassword')->getData()));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render(
            'admin/user/new.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('admin/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user, UserPasswordEncoderInterface $encoder): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $origUser = clone $user;
        $originalPassword = $user->getPassword();
        $form->handleRequest($request);
        if ($form->isSubmitted() && !$form->isValid()) {
            $errors = $this->checkUser->checkData($user, $origUser);
            if (!empty($errors)) {
                return $this->render('admin/user/edit.html.twig', [
                    'errors' => $errors,
                    'user' => $user,
                    'form' => $form->createView(),
                ]);
            }
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            if (!empty($plainPassword)) {
                $user->setPassword($encoder->encodePassword($user, $form->get('plainPassword')->getData()));
            } else {
                $user->setPassword($originalPassword);
            }
            $errors = $this->checkUser->checkData($user, $origUser);
            if (!empty($errors)) {
                return $this->render('admin/user/edit.html.twig', [
                    'errors' => $errors,
                    'user' => $user,
                    'form' => $form->createView(),
                ]);
            }

            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('user_index');
        }

        return $this->render('admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"POST"})
     */
    public
    function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }
}
