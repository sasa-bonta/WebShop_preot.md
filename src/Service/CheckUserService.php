<?php


namespace App\Service;


use App\Entity\User;
use App\Repository\UserRepository;

class CheckUserService
{
    private UserRepository $repo;
    private $errors = [];

    /**
     * CheckUserService constructor.
     */
    public function __construct(UserRepository $repo)
    {
        $this->repo = $repo;
    }

    public function checkData(User $newUser, User $origUser = null): array
    {
        $this->errors = [];
        $this->checkUsername($newUser, $origUser);
        $this->checkEmail($newUser, $origUser);
        return $this->errors;
    }

    private function checkUsername(User $newUser, User $origUser = null)
    {
        # Errors existent Nickname
        if ($this->repo->count(['username' => $newUser->getUsername()]) > 0) {
            if (isset($origUser) && $newUser->getUsername() === $origUser->getUsername()) {
                return;
            }
            $this->errors['nick'] = "This nickname already exists";
        }
    }

    private function checkEmail(User $newUser, User $origUser = null)
    {
        # Errors existent Email
        if ($this->repo->count(['email' => $newUser->getEmail()]) > 0) {
            if (isset($origUser) && $newUser->getEmail() === $origUser->getEmail()) {
                return;
            }
            $this->errors['email'] = "This e-mail address already exists";
        }
    }
}