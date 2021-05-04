<?php

namespace App\Controller;

use App\Entity\User;
use App\Exceptions\InvalidLimitException;
use App\Exceptions\InvalidPageException;
use App\Exceptions\NonexistentOrderByColumn;
use App\Exceptions\NonexistentOrderingType;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\SearchCriteria;
use App\UserSearchCriteria;
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
    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(Request $request, UserRepository $userRepository): Response
    {
        $param = $request->query->get('search');
        $page = $request->query->get('page', 1);
        $limit = $request->query->get('limit', 16);
        if ($limit > 100) {
            throw new BadRequestHttpException("400");
        }
        $orderBy = $request->query->get('order', 'email:ASC');
        $arr = explode(":", $orderBy, 2);
        $order = $arr[0];
        $ascDesc = $arr[1];

        try {
            $searchUser = new UserSearchCriteria($param, $page, $limit, $order, $ascDesc);
        } catch (Exception $e) {
            throw new BadRequestHttpException("400");
        }

        $length = $userRepository->countTotal($searchUser);
        if ($page > ceil($length / $limit) and $length / $limit !== 0) {
            throw new BadRequestHttpException("400");
        }

//        var_dump($userRepository->search($searchUser));

        return $this->render('admin/user/index.html.twig', [
            'users' => $userRepository->search($searchUser),
            'length' => $length,
            'limit' => $searchUser->getLimit()
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $encoder, UserRepository $repo): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        # catching errors
        $pass1 = $form->get('plainPassword')->getData();
        $pass2 = $form->get('password')->getData();
        if ($form->isSubmitted() && $form->isValid()) {
            $repo = $this->getDoctrine()->getRepository(User::class);
            $errors = [];
            if ($repo->count(['username' => $user->getUsername()]) > 0) {
                $errors["nick"] = "This nickname already exists";
            }
            if (mb_strlen($user->getUsername()) < 1 or mb_strlen($user->getUsername()) > 30) {
                $errors["nick"] = "The nickname should contain from 1 to 30 characters";
            }
            if ($repo->count(['email' => $user->getEmail()]) > 0) {
                $errors["email"] = "This e-mail address already exists";
            }
            if (mb_strlen($pass1) < 8 or mb_strlen($pass1) > 255 or mb_strlen($pass2) < 8 or mb_strlen($pass2) > 255) {
                $errors["pass1"] = "The password should contain from 8 to 255 characters";
            }
            if ($pass1 !== $pass2) {
                $errors["pass2"] = "The passwords don't match";
            }
            if (!empty($errors)) {
                return $this->render('admin/user/new.html.twig', [
                    'errors' => $errors,
                    'user' => $user,
                    'form' => $form->createView(),
                ]);
            }

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
        $origNick = $user->getUsername();
        $origEmail = $user->getEmail();
        $originalPassword = $user->getPassword();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pass1 = $form->get('plainPassword')->getData();
            $pass2 = $form->get('password')->getData();
            if ($pass1 !== null && $pass2 !== null) {
                if (mb_strlen($pass1) < 8 or mb_strlen($pass1) > 255 or mb_strlen($pass2) < 8 or mb_strlen($pass2) > 255) {
                    $errors["pass1"] = "The password should contain from 8 to 255 characters";
                }
                if ($pass1 !== $pass2) {
                    $errors["pass2"] = "The passwords don't match";
                }
                if (!empty($errors)) {
                    $user->setPassword($encoder->encodePassword($user, $form->get('plainPassword')->getData()));
                }
            } else {
                $user->setPassword($originalPassword);
            }
            $repo = $this->getDoctrine()->getRepository(User::class);
            if ($repo->count(['username' => $user->getUsername()]) > 0 and $form->get('username')->getData() !== $origNick) {
                $errors["nick"] = "This nickname already exists";
            }
            if ($repo->count(['email' => $user->getEmail()]) > 0 and $form->get('email')->getData() !== $origEmail) {
                $errors["email"] = "This e-mail address already exists";
            }

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
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }
}
