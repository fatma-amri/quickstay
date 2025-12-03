<?php

require_once __DIR__ . '/../models/User.php'; // Require the User model

class UserController {

    public static function listUsers() {
        // Fetch users from the database using the User model
        $users = User::getAll();

       
    }

 
}

?>
