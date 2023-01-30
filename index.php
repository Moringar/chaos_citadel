<?php

namespace App;

require 'vendor/autoload.php';

use App\Controllers\UserController;
use App\Models\Database;
use Dotenv\Dotenv;
use ReallySimpleJWT\Token;

// Loads the .env file
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

Database::$host = $_ENV['DBHOST'];
Database::$user = $_ENV['DBUSER'];
Database::$pass = $_ENV['DBPASSWORD'];
Database::$dbName = $_ENV['DBNAME'];
Database::connect();

$secret = $_ENV['SECRET'];

//--------------------------------------------------------- ROUTER ------------------------------------------------

// CREATES A NEW USER, WITH PROVIDED PSEUDO, MAIL AND PASSWORD ====================================================
if ($_SERVER['REQUEST_URI'] == "/user/create")
{
    // IF CREDENTIALS ARE PROVIDED
    if( isset($_POST["pseudo"]) and isset($_POST["mail"]) and isset($_POST["password"]) )
    {
        $pseudo = $_POST["pseudo"];
        $mail = $_POST["mail"];
        $password = $_POST["password"];

        $user = new UserController();
        $user = $user->getUserByPseudo($pseudo);

        // If the user exists already, error
        if($user != [])
        {
            echo json_encode(["message" => "User already exists"]);
        }
        // Else, creates the user and it's inventory
        else
        {
            $user = new UserController();
            $userAssets = new UserController();
            $user->createUser($pseudo, $mail, $password);
            
            $user = $user->getUserByPseudo($pseudo);
            $userId = $user[0]["id"];
            $userAssets->createUserAssets($userId);

            echo json_encode(["message" => "user created"]);
        }
    }
    // ELSE, IF MISSING CREDENTIALS
    else
    {
        echo json_encode(["message" => "must provide credentials"]);
    } 
}



// AUTHENTICATES A USER WITH USERNAME AND PASSWORD and returns a token ====================================
if ($_SERVER['REQUEST_URI'] == "/user/authenticate")
{
    // If credentials are provided
    if( isset($_POST["pseudo"]) and isset($_POST["password"]) )
    {
        $pseudo = $_POST["pseudo"];
        $password = $_POST["password"];

        $user = new UserController();
        $user = $user->getUserByPseudo($pseudo);
        
        // If there is a user returned, corresponding to the user credentials
        if($user != [])
        {
            // If user password is verified
            if($user[0]["password"] == $password )
            {
                
                // Creates and delivers a token
                $token_id = $user[0]["id"];
                $token_secret = $secret;
                $token_expiration = time() + 86400;
                $token_issuer = "localhost";
                $token = Token::create($token_id, $token_secret, $token_expiration, $token_issuer);
                echo json_encode(["token" => $token]);

            }
            else
            {
                echo json_encode(["message" => "Invalid username or password"]);
            }
        }

        // ELSE user and password are invalid, because nothing is returned
        else
        {
            echo json_encode(["message" => "Invalid username or password"]);
        }
    }
    // Else, credentials are not provided
    else
    {
        echo json_encode(["message" => "Must provide credentials"]);
    }
}


// Deletes a user with it's inventory and formula ====================================================
if ($_SERVER['REQUEST_URI'] == "/user/delete")
{
    if( isset($_POST["token"]))
    {
        $token = $_POST["token"];
        $token_secret = $secret;

        $result = Token::validate($token, $token_secret);

        // Si le token est valide, 
        if($result)
        {
            $payload = Token::getPayload($token);
            $user_id = $payload["user_id"];

            $user = new UserController();
            $user = $user->deleteUserById($user_id);
            echo json_encode(["message" => "User deleted"]);
        }
        else
        {
            echo json_encode(["message" => "INVALID TOKEN"]);
        }
    }
    else
    {
        echo json_encode(["message" => "MUST PROVIDE TOKEN"]);
    }
    
}



// Get user informations and characteristics from the user table ====================================================
if ($_SERVER['REQUEST_URI'] == "/user/get_info")
{
    if( isset($_POST["token"]))
    {
        $token = $_POST["token"];
        $token_secret = $secret;

        $result = Token::validate($token, $token_secret);

        // Si le token est valide, 
        if($result)
        {
            $payload = Token::getPayload($token);
            $user_id = $payload["user_id"];

            $user = new UserController();
            $user = $user->getUserById($user_id);
            echo json_encode($user);

        }
        else
        {
            echo json_encode(["message" => "INVALID TOKEN"]);
        }
    }
    else
    {
        echo json_encode(["message" => "MUST PROVIDE TOKEN"]);
    }
    
}

// Get formula of a user from the formula table, by it's user_id =====================================================
if ($_SERVER['REQUEST_URI'] == "/user/get_formula")
{
    if( isset($_POST["token"]))
    {
        $token = $_POST["token"];
        $token_secret = $secret;

        $result = Token::validate($token, $token_secret);

        // Si le token est valide, 
        if($result)
        {
            $payload = Token::getPayload($token);
            $user_id = $payload["user_id"];

            $user = new UserController();
            $user = $user->getFormulaByUserId($user_id);
            echo json_encode($user);
            // $user = new UserController();
            // $user = $user->getUserById($user_id);
            // echo json_encode($user);
        }
        else
        {
            echo json_encode(["message" => "INVALID TOKEN"]);
        }
    }
    else
    {
        echo json_encode(["message" => "MUST PROVIDE TOKEN"]);
    }
    
}


// Get inventory of a user from the inventory table, by it's user_id ==============================================
if ($_SERVER['REQUEST_URI'] == "/user/get_inventory")
{
    if( isset($_POST["token"]))
    {
        $token = $_POST["token"];
        $token_secret = $secret;

        $result = Token::validate($token, $token_secret);

        // Si le token est valide, 
        if($result)
        {
            $payload = Token::getPayload($token);
            $user_id = $payload["user_id"];

            $user = new UserController();
            $user = $user->getInventoryByUserId($user_id);
            echo json_encode($user);
            // $user = new UserController();
            // $user = $user->getUserById($user_id);
            // echo json_encode($user);
        }
        else
        {
            echo json_encode(["message" => "INVALID TOKEN"]);
        }
    }
    else
    {
        echo json_encode(["message" => "MUST PROVIDE TOKEN"]);
    }
    
}


// Sets inventory of a user by it's user_id ===================================================================
if ($_SERVER['REQUEST_URI'] == "/user/set_inventory")
{
    if( isset($_POST["token"]) and
    isset($_POST["myriad"]) and
    isset($_POST["spider_jar"]) and
    isset($_POST["berry"]) and
    isset($_POST["enchanted_dagger"]) and
    isset($_POST["fire_wine"]) and
    isset($_POST["berce_bottle"]) and
    isset($_POST["green_liquid_bottle"]) and
    isset($_POST["golden_fleece"]) and
    isset($_POST["silver_mirror"]) and
    isset($_POST["comb"]) and
    isset($_POST["gold"]) and
    isset($_POST["enchanted_amulet"]) and
    isset($_POST["copper_key"])
    )
    {
        $myriad = $_POST["myriad"];
        $spider_jar = $_POST["spider_jar"];
        $berry = $_POST["berry"];
        $enchanted_dagger = $_POST["enchanted_dagger"];
        $fire_wine = $_POST["fire_wine"];
        $berce_bottle = $_POST["berce_bottle"];
        $green_liquid_bottle = $_POST["green_liquid_bottle"];
        $golden_fleece = $_POST["golden_fleece"];
        $silver_mirror = $_POST["silver_mirror"];
        $comb = $_POST["comb"];
        $gold = $_POST["gold"];
        $enchanted_amulet = $_POST["enchanted_amulet"];
        $copper_key = $_POST["copper_key"];


        $token = $_POST["token"];
        $token_secret = $secret;
        $result = Token::validate($token, $token_secret);

        // Si le token est valide, 
        if($result)
        {
            $payload = Token::getPayload($token);
            $user_id = $payload["user_id"];

            $user = new UserController();
            $user = $user->setInventoryByUserId($myriad, $spider_jar, $berry, $enchanted_dagger, $fire_wine, $berce_bottle, $green_liquid_bottle, $golden_fleece, $silver_mirror, $comb, $gold, $enchanted_amulet, $copper_key, $user_id);
            echo json_encode(["message" => "Inventory updated"]);
            // $user = new UserController();
            // $user = $user->getUserById($user_id);
            // echo json_encode($user);
        }
        else
        {
            echo json_encode(["message" => "INVALID TOKEN"]);
        }
    }
    else
    {
        echo json_encode(["message" => "MUST PROVIDE TOKEN AND INVENTORY DATA"]);
    }
}


// Sets Formulas of a user by it's user_id ======================================================================
if ($_SERVER['REQUEST_URI'] == "/user/set_formula")
{
    if( isset($_POST["token"]) and
    isset($_POST["formula_luck"]) and
    isset($_POST["formula_copy"]) and
    isset($_POST["formula_life"]) and
    isset($_POST["formula_weakness"]) and
    isset($_POST["formula_fire"]) and
    isset($_POST["formula_force"]) and
    isset($_POST["formula_ability"]) and
    isset($_POST["formula_illusion"]) and
    isset($_POST["formula_levitation"]) and
    isset($_POST["formula_gold"]) and
    isset($_POST["formula_protection"]) and
    isset($_POST["formula_telepathy"])
    )
    {
        $formula_luck = $_POST["formula_luck"];
        $formula_copy = $_POST["formula_copy"];
        $formula_life = $_POST["formula_life"];
        $formula_weakness = $_POST["formula_weakness"];
        $formula_fire = $_POST["formula_fire"];
        $formula_force = $_POST["formula_force"];
        $formula_ability = $_POST["formula_ability"];
        $formula_illusion = $_POST["formula_illusion"];
        $formula_levitation = $_POST["formula_levitation"];
        $formula_gold = $_POST["formula_gold"];
        $formula_protection = $_POST["formula_protection"];
        $formula_telepathy = $_POST["formula_telepathy"];


        $token = $_POST["token"];
        $token_secret = $secret;
        $result = Token::validate($token, $token_secret);

        // Si le token est valide, 
        if($result)
        {
            $payload = Token::getPayload($token);
            $user_id = $payload["user_id"];

            $user = new UserController();
            $user = $user->setFormulaByUserId($formula_luck, $formula_copy, $formula_life, $formula_weakness, $formula_fire, $formula_force, $formula_ability, $formula_illusion, $formula_levitation, $formula_gold, $formula_protection, $formula_telepathy, $user_id);
            echo json_encode(["message" => "Formula updated"]);
            // $user = new UserController();
            // $user = $user->getUserById($user_id);
            // echo json_encode($user);
        }
        else
        {
            echo json_encode(["message" => "INVALID TOKEN"]);
        }
    }
    else
    {
        echo json_encode(["message" => "MUST PROVIDE TOKEN AND INVENTORY DATA"]);
    }
}

// Sets characteristics of a user by it's user_id ======================================================================
if ($_SERVER['REQUEST_URI'] == "/user/set_characteristics")
{
    if( isset($_POST["token"]) and
    isset($_POST["ability_max"]) and
    isset($_POST["ability_current"]) and
    isset($_POST["life_max"]) and
    isset($_POST["life_current"]) and
    isset($_POST["chance_max"]) and
    isset($_POST["chance_current"]) and
    isset($_POST["magic_max"]) and
    isset($_POST["magic_current"])
    )
    {
        $ability_max = $_POST["ability_max"];
        $ability_current = $_POST["ability_current"];
        $life_max = $_POST["life_max"];
        $life_current = $_POST["life_current"];
        $chance_max = $_POST["chance_max"];
        $chance_current = $_POST["chance_current"];
        $magic_max = $_POST["magic_max"];
        $magic_current = $_POST["magic_current"];


        $token = $_POST["token"];
        $token_secret = $secret;
        $result = Token::validate($token, $token_secret);

        // Si le token est valide, 
        if($result)
        {
            $payload = Token::getPayload($token);
            $user_id = $payload["user_id"];

            $user = new UserController();
            $user = $user->setUserCharacteristicsByUserId($ability_max, $ability_current, $life_max, $life_current, $chance_max, $chance_current, $magic_max, $magic_current, $user_id);
            echo json_encode(["message" => "Formula updated"]);

        }
        else
        {
            echo json_encode(["message" => "INVALID TOKEN"]);
        }
    }
    else
    {
        echo json_encode(["message" => "MUST PROVIDE TOKEN AND INVENTORY DATA"]);
    }
}

// Sets current step of a user by it's user_id ======================================================================
if ($_SERVER['REQUEST_URI'] == "/user/set_step")
{
    if( isset($_POST["token"]) and
    isset($_POST["step"])
    )
    {
        $step = $_POST["step"];


        $token = $_POST["token"];
        $token_secret = $secret;
        $result = Token::validate($token, $token_secret);

        // Si le token est valide, 
        if($result)
        {
            $payload = Token::getPayload($token);
            $user_id = $payload["user_id"];

            $user = new UserController();
            $user = $user->setUserStepByUserId($step, $user_id);
            echo json_encode(["message" => "step updated"]);
        }
        else
        {
            echo json_encode(["message" => "INVALID TOKEN"]);
        }
    }
    else
    {
        echo json_encode(["message" => "MUST PROVIDE TOKEN AND INVENTORY DATA"]);
    }
}


