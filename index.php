<?php

namespace App;

require 'vendor/autoload.php';

use App\Models\Database;
use Dotenv\Dotenv;

// Loads the .env file
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

Database::$host = $_ENV['DBHOST'];
Database::$user = $_ENV['DBUSER'];
Database::$pass = $_ENV['DBPASSWORD'];
Database::$dbName = $_ENV['DBNAME'];
Database::connect();

//--------------------------------------------------------- ROUTER ------------------------------------------------

// Return a hello world
if ($_SERVER['REQUEST_URI'] == "/test") {
    if( isset($_POST["name"]) and isset($_POST["mail"]) and isset($_POST["password"]) )
    {
        $name = $_POST["name"];
        $mail = $_POST["mail"];
        $password = $_POST["password"];
        
        echo "$name $mail $password";
    }
    else
    {
        echo "must provide credentials";
    }
    
}