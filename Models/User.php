<?php

namespace App\Controllers;

use App\Models\Database;

class User{

    public function createUser($name, $mail, $password)
    {
        // Inserts a new user in user
    }

    public function getUserByName($name)
    {
        // Select user where name = name
        // returns results
    }
    public function getUserById($id)
    {
        // Select user where id = id
        // Joins inventory where user.id = inventory.user_id
        //returns user data + Joined
    }

}