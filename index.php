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

// Connects to the database with the credentials available in the .env file
Database::$host = $_ENV['DBHOST'];
Database::$user = $_ENV['DBUSER'];
Database::$pass = $_ENV['DBPASSWORD'];
Database::$dbName = $_ENV['DBNAME'];
Database::connect();

// Get the secret key for token generation in the .env file
$secret = $_ENV['SECRET'];


//--------------------------------------------------------- ROUTER ------------------------------------------------

// CREATES A NEW USER, WITH PROVIDED PSEUDO, MAIL AND PASSWORD ====================================================
// ==================================================================================================================================
if ($_SERVER['REQUEST_URI'] == "/user/create" and $_SERVER["REQUEST_METHOD"] == "POST")
{
    $input = file_get_contents("php://input");
	$_BODY = json_decode($input, true);

    // IF CREDENTIALS ARE PROVIDED
    if( isset($_BODY["pseudo"]) and isset($_BODY["mail"]) and isset($_BODY["password"]) )
    {
        $pseudo = $_BODY["pseudo"];
        $mail = $_BODY["mail"];
        $password = $_BODY["password"];

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
if ($_SERVER['REQUEST_URI'] == "/user/authenticate" and $_SERVER["REQUEST_METHOD"] == "POST")
{
    $input = file_get_contents("php://input");
	$_BODY = json_decode($input, true);
    
    // If credentials are provided
    if( isset($_BODY["pseudo"]) and isset($_BODY["password"]) )
    {
        $pseudo = $_BODY["pseudo"];
        $password = $_BODY["password"];

        $user = new UserController();
        $user = $user->getUserByPseudo($pseudo);
        
        // If there is a user returned, corresponding to the user credentials
        if($user != [])
        {
            // If user password is verified
            if(password_verify($password, $user[0]["password"]) )
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
if ($_SERVER['REQUEST_URI'] == "/user/delete" and $_SERVER["REQUEST_METHOD"] == "DELETE")
{

    $input = file_get_contents("php://input");
	$_BODY = json_decode($input, true);

    if( isset($_BODY["token"]))
    {
        $token = $_BODY["token"];

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
if ($_SERVER['REQUEST_URI'] == "/user/get_info" and $_SERVER["REQUEST_METHOD"] == "GET") 
{
    $input = file_get_contents("php://input");
	$_BODY = json_decode($input, true);

    if( isset($_BODY["token"]))
    {
        $token = $_BODY["token"];
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
if ($_SERVER['REQUEST_URI'] == "/user/get_formula" and $_SERVER["REQUEST_METHOD"] == "GET")
{
    $input = file_get_contents("php://input");
	$_BODY = json_decode($input, true);

    if( isset($_BODY["token"]))
    {
        $token = $_BODY["token"];
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
if ($_SERVER['REQUEST_URI'] == "/user/get_inventory" and $_SERVER["REQUEST_METHOD"] == "GET")
{
    $input = file_get_contents("php://input");
	$_BODY = json_decode($input, true);

    if( isset($_BODY["token"]))
    {
        $token = $_BODY["token"];
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
if ($_SERVER['REQUEST_URI'] == "/user/set_inventory" and $_SERVER["REQUEST_METHOD"] == "PUT")
{
    $input = file_get_contents("php://input");
	$_BODY = json_decode($input, true);

    if( isset($_BODY["token"]) and
    isset($_BODY["myriad"]) and
    isset($_BODY["spider_jar"]) and
    isset($_BODY["berry"]) and
    isset($_BODY["enchanted_dagger"]) and
    isset($_BODY["fire_wine"]) and
    isset($_BODY["berce_bottle"]) and
    isset($_BODY["green_liquid_bottle"]) and
    isset($_BODY["golden_fleece"]) and
    isset($_BODY["silver_mirror"]) and
    isset($_BODY["comb"]) and
    isset($_BODY["gold"]) and
    isset($_BODY["enchanted_amulet"]) and
    isset($_BODY["copper_key"])
    )
    {
        $myriad = $_BODY["myriad"];
        $spider_jar = $_BODY["spider_jar"];
        $berry = $_BODY["berry"];
        $enchanted_dagger = $_BODY["enchanted_dagger"];
        $fire_wine = $_BODY["fire_wine"];
        $berce_bottle = $_BODY["berce_bottle"];
        $green_liquid_bottle = $_BODY["green_liquid_bottle"];
        $golden_fleece = $_BODY["golden_fleece"];
        $silver_mirror = $_BODY["silver_mirror"];
        $comb = $_BODY["comb"];
        $gold = $_BODY["gold"];
        $enchanted_amulet = $_BODY["enchanted_amulet"];
        $copper_key = $_BODY["copper_key"];

        $token = $_BODY["token"];

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
        }
        else
        {
            echo json_encode(["message" => "Invalid token"]);
        }
    }
    else
    {
        echo json_encode(["message" => "Must provide token and user inventory data"]);
    }
}


// Sets Formulas of a user by it's user_id ======================================================================
if ($_SERVER['REQUEST_URI'] == "/user/set_formula" and $_SERVER["REQUEST_METHOD"] == "PUT")
{

    $input = file_get_contents("php://input");
	$_BODY = json_decode($input, true);

    if( isset($_BODY["token"]) and
    isset($_BODY["formula_luck"]) and
    isset($_BODY["formula_copy"]) and
    isset($_BODY["formula_life"]) and
    isset($_BODY["formula_weakness"]) and
    isset($_BODY["formula_fire"]) and
    isset($_BODY["formula_force"]) and
    isset($_BODY["formula_ability"]) and
    isset($_BODY["formula_illusion"]) and
    isset($_BODY["formula_levitation"]) and
    isset($_BODY["formula_gold"]) and
    isset($_BODY["formula_protection"]) and
    isset($_BODY["formula_telepathy"])
    )
    {
        $formula_luck = $_BODY["formula_luck"];
        $formula_copy = $_BODY["formula_copy"];
        $formula_life = $_BODY["formula_life"];
        $formula_weakness = $_BODY["formula_weakness"];
        $formula_fire = $_BODY["formula_fire"];
        $formula_force = $_BODY["formula_force"];
        $formula_ability = $_BODY["formula_ability"];
        $formula_illusion = $_BODY["formula_illusion"];
        $formula_levitation = $_BODY["formula_levitation"];
        $formula_gold = $_BODY["formula_gold"];
        $formula_protection = $_BODY["formula_protection"];
        $formula_telepathy = $_BODY["formula_telepathy"];

        $token = $_BODY["token"];

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

    $input = file_get_contents("php://input");
	$_BODY = json_decode($input, true);


    if( isset($_BODY["token"]) and
    isset($_BODY["ability_max"]) and
    isset($_BODY["ability_current"]) and
    isset($_BODY["life_max"]) and
    isset($_BODY["life_current"]) and
    isset($_BODY["chance_max"]) and
    isset($_BODY["chance_current"]) and
    isset($_BODY["magic_max"]) and
    isset($_BODY["magic_current"])
    )
    {
        $ability_max = $_BODY["ability_max"];
        $ability_current = $_BODY["ability_current"];
        $life_max = $_BODY["life_max"];
        $life_current = $_BODY["life_current"];
        $chance_max = $_BODY["chance_max"];
        $chance_current = $_BODY["chance_current"];
        $magic_max = $_BODY["magic_max"];
        $magic_current = $_BODY["magic_current"];


        $token = $_BODY["token"];

        $token_secret = $secret;
        $result = Token::validate($token, $token_secret);

        // Si le token est valide, 
        if($result)
        {
            $payload = Token::getPayload($token);
            $user_id = $payload["user_id"];

            $user = new UserController();
            $user = $user->setUserCharacteristicsByUserId($ability_max, $ability_current, $life_max, $life_current, $chance_max, $chance_current, $magic_max, $magic_current, $user_id);
            echo json_encode(["message" => "User characteristics updated"]);

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

    
    $input = file_get_contents("php://input");
	$_BODY = json_decode($input, true);


    if( isset($_BODY["token"]) and
    isset($_BODY["step"])
    )
    {
        $step = $_BODY["step"];

        $token = $_BODY["token"];

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
            echo json_encode(["message" => "Invalid token"]);
        }
    }
    else
    {
        echo json_encode(["message" => "Must provide token and step data"]);
    }
}


