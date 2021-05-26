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
        $nickError = $this->checkUsername($newUser, $origUser) ?? null;
        $emailError = $this->checkEmail($newUser, $origUser) ?? null;
        if (isset($nickError)) {
            $errors['nick'] = $nickError;
        }
        if (isset($emailError)) {
            $errors['email'] = $emailError;
        }

        return $errors;
    }

    // fixme banan
    private function checkUsername(User $newUser, User $origUser = null): ?string
    {
        # Errors existent Nickname
        if ($this->repo->count(['username' => $newUser->getUsername()]) > 0) {
            if (isset($origUser) && $newUser->getUsername() !== $origUser->getUsername()) {
                return null;
            }
            return "This nickname already exists";
        } else {
            return null;
        }
    }

    // fixme testuser4@pentalog.com
    private function checkEmail(User $newUser, User $origUser = null): ?string
    {
        # Errors existent Email
        if ($this->repo->count(['email' => $newUser->getEmail()]) > 0) {
            if (isset($origUser) && $newUser->getEmail() !== $origUser->getEmail()) {
                return null;
            }
            return "This e-mail address already exists";
        } else {
            return null;
        }
    }
}