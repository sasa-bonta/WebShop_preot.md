<?php


namespace App\Service;


use App\Entity\User;
use App\Repository\UserRepository;

class CheckUserService
{
    private UserRepository $repo;

    /**
     * CheckUserService constructor.
     */
    public function __construct(UserRepository $repo)
    {
        $this->repo = $repo;
    }

    public function checkData(User $newUser, User $origUser = null): array
    {
        $errors = [];

        $nickError = $this->checkUsername($newUser, $origUser);
        if (isset($nickError)) {
            $errors['nick'] = $nickError;
        }

        $emailError = $this->checkEmail($newUser, $origUser);
        if (isset($emailError)) {
            $errors['email'] = $emailError;
        }
        return $errors;
    }

    private function checkUsername(User $newUser, User $origUser = null)
    {
        # Errors existent Nickname
        $nickError = null;
        if ($this->repo->count(['username' => $newUser->getUsername()]) > 0) {
            if (isset($origUser) && $newUser->getUsername() === $origUser->getUsername()) {
                return null;
            }
            $nickError = "This nickname already exists";
        }
        return $nickError;
    }

    private function checkEmail(User $newUser, User $origUser = null)
    {
        # Errors existent Email
        $emailError = null;
        if ($this->repo->count(['email' => $newUser->getEmail()]) > 0) {
            if (isset($origUser) && $newUser->getEmail() === $origUser->getEmail()) {
                return null;
            }
            $emailError = "This e-mail address already exists";
        }
        return $emailError;
    }
}