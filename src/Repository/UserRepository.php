<?php
namespace FinanceApp\Repository;

use FinanceApp\Model\User;

class UserRepository extends AbstractRepository
{

    public function getUserByEmail($email)
    {
        $userRow = $this->dbConnection->fetchArray(
            'SELECT id, email, password, name FROM user WHERE email = ?', [$email]
        );

        return $userRow[0] !== null ?
            new User($userRow[0], $userRow[1], $userRow[2], $userRow[3]) :
            null;
    }

    public function saveUser(User $user)
    {
        if ($user->id !== null) {
            $this->dbConnection->executeQuery(
                'UPDATE user SET email = ?, name = ? WHERE id = ?',
                [$user->email, $user->name, $user->id]
            );
        } else {
            $this->dbConnection->executeQuery(
                'INSERT INTO user (email, password, name) VALUES (?, ?, ?)',
                [$user->email, $user->password, $user->name]
            );

            $user->id = $this->dbConnection->lastInsertId();
        }


        return $user;
    }

}
