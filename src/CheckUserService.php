<?php


namespace App;


use App\Entity\User;

class CheckUserService
{

    /**
     * CheckUserService constructor.
     */
    public function __construct()
    {
    }

    function checkData(User $user): array
    {
        # Errors existent Nickname and/or Email
        $errors = [];
        $repo = $this->getDoctrine()->getRepository(User::class);
        if ($repo->count(['username' => $user->getUsername()]) > 0) {
            $errors['nick'] = "This nickname already exists";
        }
        if ($repo->count(['email' => $user->getEmail()]) > 0) {
            $errors['email'] = "This e-mail address already exists";
        }
        return $errors;
    }
}