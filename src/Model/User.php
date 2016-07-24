<?php
namespace FinanceApp\Model;

class User
{
    public $id;
    public $email;
    public $password;
    public $name;

    public function __construct($id, $email, $password, $name)
    {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->name = $name;
    }
}
