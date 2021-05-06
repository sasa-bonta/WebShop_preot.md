<?php

namespace App\Controller;

use App\CheckUserService;
use App\Entity\User;
use App\Form\UserType;
use Exception;
use App\Repository\UserRepository;
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
    private CheckUserService $checkUser;

    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(Request $request, UserRepository $userRepository): Response
    {
        $param = $request->query->get('search');
        $page = $request->query->get('page', 1);
        $limit = $request->query->get('limit', 16);
        if ($limit > 120) {
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

    function checkDataEditUser(User $user, $origNick, $origEmail, $form): array
    {
        # Errors existent Nickname and/or Email
        $errors = [];
        $repo = $this->getDoctrine()->getRepository(User::class);
        if ($repo->count(['username' => $user->getUsername()]) > 0 and $form->get('username')->getData() !== $origNick) {
            $errors['nick'] = "This nickname already exists";
        }
        if ($repo->count(['email' => $user->getEmail()]) > 0 and $form->get('email')->getData() !== $origEmail) {
            $errors['email'] = "This e-mail address already exists";
        }
        return $errors;
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

        if ($form->isSubmitted() && !$form->isValid()) {
            $errors = $this->checkDataEditUser($user, $origNick, $origEmail, $form);
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
            $errors = $this->checkDataEditUser($user, $origNick, $origEmail, $form);
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
