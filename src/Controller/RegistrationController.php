<?php


namespace App\Controller;


use App\Entity\User;
use App\Form\RegisterType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class RegistrationController extends AbstractController
{
    function checkData(User $user, UserRepository $repo): array
    {
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
        if (mb_strlen($user->getPlainPassword()) < 8 or mb_strlen($user->getPlainPassword() > 255)) {
            $errors["pass1"] = "The password should contain from 8 to 255 characters";
        }
        return $errors;
    }

    /**
     * @Route("/register", name="user_registration")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, UserRepository $repo)
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);
        $errors = [];
        # catching errors
        if (!$form->isValid()) {
            $form->getErrors();
            $errors = $this->checkData($user, $repo);
            $errors["pass2"] = "The passwords don't match";
            return $this->render('registration/register.html.twig', [
                'errors' => $errors,
                'user' => $user,
                'form' => $form->createView(),
            ]);
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $repo = $this->getDoctrine()->getRepository(User::class);
            $errors = $this->checkData($user, $repo);

            if (!empty($errors)) {
                return $this->render('registration/register.html.twig', [
                    'errors' => $errors,
                    'user' => $user,
                    'form' => $form->createView(),
                ]);
            }

            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render(
            'registration/register.html.twig',
            ['form' => $form->createView()]
        );
    }
}