<?php
namespace FinanceApp\Service;

use FinanceApp\Model\User;
use FinanceApp\Repository\UserRepository;

class UserService
{

    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function createUser($email, $password, $name)
    {
        $user = new User(null, $email, $password, $name);

        $user = $this->userRepository->saveUser($user);

        return $user;
    }

    public function getUserByEmail($email)
    {
        return $this->userRepository->getUserByEmail($email);
    }

    public function updateUser(User $user, $email, $name)
    {
        $user->email = $email;
        $user->name = $name;

        $user = $this->userRepository->saveUser($user);

        return $user;
    }

}