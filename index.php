<?php

namespace App;
header('Access-Control-Allow-Origin: *');
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

// Creates a nea user with provided pseudo, mail and password / POST METHOD =========================================================
// ==================================================================================================================================
if ($_SERVER['REQUEST_URI'] == "/user/create" and $_SERVER["REQUEST_METHOD"] == "POST")
{   
    // Gets the body of the request, and turns the received json into an associative array
    $input = file_get_contents("php://input");
	$_BODY = json_decode($input, true);

    // If the credentials are present in the body of the request
    if( isset($_BODY["pseudo"]) and isset($_BODY["mail"]) and isset($_BODY["password"]) )
    {
        $pseudo = $_BODY["pseudo"];
        $mail = $_BODY["mail"];
        $password = $_BODY["password"];

        $user = new UserController();
        $user = $user->getUserByPseudo($pseudo);

        // If the user exists already, returns an error
        if($user != [])
        {
            echo json_encode(["message" => "User already exists"]);
            http_response_code(400);
        }
        // Else, creates the user with it's associated inventory and formula
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
    // Else, credentials are missing
    else
    {
        echo json_encode(["message" => "must provide credentials"]);
        http_response_code(400);
    } 
}



// Authenticates a user with provided pseudo and password / POST METHOD ============================================================
// ==================================================================================================================================
if ($_SERVER['REQUEST_URI'] == "/user/authenticate" and $_SERVER["REQUEST_METHOD"] == "POST")
{
    // Gets the body of the request, and turns the received json into an associative array
    $input = file_get_contents("php://input");
	$_BODY = json_decode($input, true);
    
    // If credentials are present in the body of the request
    if( isset($_BODY["pseudo"]) and isset($_BODY["password"]) )
    {
        $pseudo = $_BODY["pseudo"];
        $password = $_BODY["password"];

        $user = new UserController();
        $user = $user->getUserByPseudo($pseudo);
        
        // If there is a user returned, corresponding to the user credentials
        if($user != [])
        {
            // If the user passwords match the one stored in the database
            if(password_verify($password, $user[0]["password"]) )
            {
                
                // Creates and delivers a token
                $token_id = $user[0]["id"];
                $token_secret = $secret;
                $token_expiration = time() + 86400;
                $token_issuer = "localhost";
                $token = Token::create($token_id, $token_secret, $token_expiration, $token_issuer);
                //Token sent
                echo json_encode(["token" => $token]);

            }
            // The password is invalid
            else
            {
                echo json_encode(["message" => "Invalid username or password"]);
            }
        }

        // else, there is no user found with provided credentials
        else
        {
            echo json_encode(["message" => "Invalid username or password"]);
            http_response_code(401);
        }
    }
    // else, credentials are not provided
    else
    {
        echo json_encode(["message" => "Must provide credentials"]);
        http_response_code(400);
    }
}


// Deletes a user with it's associated inventory and formula / DELETE METHOD ========================================================
// ==================================================================================================================================

if ($_SERVER['REQUEST_URI'] == "/user/delete" and $_SERVER["REQUEST_METHOD"] == "DELETE")
{
    // Gets the body of the request, and turns the received json into an associative array
    $input = file_get_contents("php://input");
	$_BODY = json_decode($input, true);

    // If the token is provided
    if( isset($_BODY["token"]))
    {
        // Gets the token from the body of the request and the secret key from the env file.
        // Checks if the token is valid.
        $token = $_BODY["token"];
        $token_secret = $secret;
        $result = Token::validate($token, $token_secret);

        // If the token is valid
        if($result)
        {
            // Gets the user_id stored in the payload of the token
            $payload = Token::getPayload($token);
            $user_id = $payload["user_id"];

            $user = new UserController();
            $user = $user->deleteUserById($user_id);
            echo json_encode(["message" => "User deleted"]);
        }
        // if the token is invalid
        else
        {
            echo json_encode(["message" => "INVALID TOKEN"]);
            http_response_code(401);
        }
    }
    // Else, token is missing from the body of the request
    else
    {
        echo json_encode(["message" => "MUST PROVIDE TOKEN"]);
        http_response_code(400);
    }
    
}



// Get user informations and characteristics from the user table / GET METHOD =======================================================
// ==================================================================================================================================

if ($_SERVER['REQUEST_URI'] == "/user/get_info" and $_SERVER["REQUEST_METHOD"] == "GET") 
{   
    // Gets the body of the request, and turns the received json into an associative array
    $input = file_get_contents("php://input");
	$_BODY = json_decode($input, true);

    // If the token is present in the body of the request
    if( isset($_BODY["token"]))
    {
        // Gets the token from the body of the request and the secret key from the env file.
        // Checks if the token is valid.
        $token = $_BODY["token"];
        $token_secret = $secret;

        $result = Token::validate($token, $token_secret);

        // if the token is valid
        if($result)
        {
            // Gets the user_id stored in the payload of the token
            $payload = Token::getPayload($token);
            $user_id = $payload["user_id"];

            $user = new UserController();
            $user = $user->getUserById($user_id);

            if($user == [])
            {
                echo json_encode(["message" => "User doesn't exists"]);
                http_response_code(400);
            }   
            else
            {
                echo json_encode($user);
            }

        }
        // else, the token is invalid
        else
        {
            echo json_encode(["message" => "Invalid token"]);
            http_response_code(401);
        }
    }
    // else, the token is missing
    else
    {
        echo json_encode(["message" => "Must provide token"]);
        http_response_code(400);
    }
    
}

// Get formula of a user from the formula table, by it's user_id / GET METHOD =======================================================
// ==================================================================================================================================

if ($_SERVER['REQUEST_URI'] == "/user/get_formula" and $_SERVER["REQUEST_METHOD"] == "GET")
{
    // Gets the body of the request, and turns the received json into an associative array
    $input = file_get_contents("php://input");
	$_BODY = json_decode($input, true);

    // If the token is present in the body of the request
    if( isset($_BODY["token"]))
    {
        // Gets the token from the body of the request and the secret key from the env file.
        // Checks if the token is valid.
        $token = $_BODY["token"];
        $token_secret = $secret;

        $result = Token::validate($token, $token_secret);

        // if the token is valid
        if($result)
        {
            // Gets the user_id stored in the payload of the token
            $payload = Token::getPayload($token);
            $user_id = $payload["user_id"];

            $user = new UserController();
            $user = $user->getFormulaByUserId($user_id);

            if($user == [])
            {
                echo json_encode(["message" => "User doesn't exists"]);
                http_response_code(400);
            }   
            else
            {
                echo json_encode($user);
            }
            
        }
        // else, the token is invalid
        else
        {
            echo json_encode(["message" => "INVALID TOKEN"]);
            http_response_code(401);
        }
    }
    // else, the token is missing
    else
    {
        echo json_encode(["message" => "MUST PROVIDE TOKEN"]);
        http_response_code(400);
    }
    
}


// Get inventory of a user from the inventory table, by it's user_id / GET METHOD ==================================================
// ==================================================================================================================================
if ($_SERVER['REQUEST_URI'] == "/user/get_inventory" and $_SERVER["REQUEST_METHOD"] == "GET")
{
    // Gets the body of the request, and turns the received json into an associative array
    $input = file_get_contents("php://input");
	$_BODY = json_decode($input, true);

    // If the token is present in the body of the request
    if( isset($_BODY["token"]))
    {
        // Gets the token from the body of the request and the secret key from the env file.
        // Checks if the token is valid.
        $token = $_BODY["token"];
        $token_secret = $secret;

        $result = Token::validate($token, $token_secret);

        // if the token is valid
        if($result)
        {
            // Gets the user_id stored in the payload of the token
            $payload = Token::getPayload($token);
            $user_id = $payload["user_id"];

            $user = new UserController();
            $user = $user->getInventoryByUserId($user_id);
            if($user == [])
            {
                echo json_encode(["message" => "User doesn't exists"]);
                http_response_code(400);
            }   
            else
            {
                echo json_encode($user);
            }
        }
        // else, the token is invalid
        else
        {
            echo json_encode(["message" => "Invalid token"]);
            http_response_code(401);
        }
    }
    // else, the token is not provided
    else
    {
        echo json_encode(["message" => "Must provide token"]);
        http_response_code(400);
    }
    
}


// Sets inventory of a user by it's user_id / PUT METHOD ===========================================================================
// ==================================================================================================================================
if ($_SERVER['REQUEST_URI'] == "/user/set_inventory" and $_SERVER["REQUEST_METHOD"] == "PUT")
{
    // Gets the body of the request, and turns the received json into an associative array
    $input = file_get_contents("php://input");
	$_BODY = json_decode($input, true);

    // If the token and the needed data to set the inventory are provided
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
        // Get the inventory data from the body of the request
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

        // Gets the token from the body of the request and the secret key from the env file.
        // Checks if the token is valid.
        $token = $_BODY["token"];
        $token_secret = $secret;
        $result = Token::validate($token, $token_secret);

        // if the token is valid
        if($result)
        {
            // Gets the user_id stored in the payload of the token
            $payload = Token::getPayload($token);
            $user_id = $payload["user_id"];

            $user = new UserController();
            $user = $user->setInventoryByUserId($myriad, $spider_jar, $berry, $enchanted_dagger, $fire_wine, $berce_bottle, $green_liquid_bottle, $golden_fleece, $silver_mirror, $comb, $gold, $enchanted_amulet, $copper_key, $user_id);
            echo json_encode(["message" => "Inventory updated"]);
        }
        else
        // else, the token is invalid
        {
            echo json_encode(["message" => "Invalid token"]);
            http_response_code(401);
        }
    }
    // else, data about the inventory and or token are missing
    else
    {
        echo json_encode(["message" => "Must provide token and inventory data"]);
        http_response_code(400);
    }
}


// Sets Formulas of a user by it's user_id / METHOD PUT ============================================================================
// ==================================================================================================================================
if ($_SERVER['REQUEST_URI'] == "/user/set_formula" and $_SERVER["REQUEST_METHOD"] == "PUT")
{
    // Gets the body of the request, and turns the received json into an associative array
    $input = file_get_contents("php://input");
	$_BODY = json_decode($input, true);

     // If the token and the needed data to set the formula are provided
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
        // Get the formula data from the body of the request
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

        // Gets the token from the body of the request and the secret key from the env file.
        // Checks if the token is valid.
        $token = $_BODY["token"];

        $token_secret = $secret;
        $result = Token::validate($token, $token_secret);

        // If the token is valid 
        if($result)
        {
            // Gets the user_id stored in the payload of the token
            $payload = Token::getPayload($token);
            $user_id = $payload["user_id"];

            $user = new UserController();
            $user = $user->setFormulaByUserId($formula_luck, $formula_copy, $formula_life, $formula_weakness, $formula_fire, $formula_force, $formula_ability, $formula_illusion, $formula_levitation, $formula_gold, $formula_protection, $formula_telepathy, $user_id);
            echo json_encode(["message" => "Formula updated"]);
        }
        // The token is invalid
        else
        {
            echo json_encode(["message" => "Invalid token"]);
            http_response_code(401);
        }
    }
    // else, data about the formulas and or token are missing
    else
    {
        echo json_encode(["message" => "Must provide token and formulas data"]);
        http_response_code(400);
    }
}

// Sets characteristics of a user by it's user_id / PUT METHOD ======================================================================
// ==================================================================================================================================

if ($_SERVER['REQUEST_URI'] == "/user/set_characteristics")
{
    // Gets the body of the request, and turns the received json into an associative array
    $input = file_get_contents("php://input");
	$_BODY = json_decode($input, true);

    // If the token and the needed data to set the user characteristics are provided
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
        // Get the user characteristics data from the body of the request
        $ability_max = $_BODY["ability_max"];
        $ability_current = $_BODY["ability_current"];
        $life_max = $_BODY["life_max"];
        $life_current = $_BODY["life_current"];
        $chance_max = $_BODY["chance_max"];
        $chance_current = $_BODY["chance_current"];
        $magic_max = $_BODY["magic_max"];
        $magic_current = $_BODY["magic_current"];

        // Gets the token from the body of the request and the secret key from the env file.
        // Checks if the token is valid.
        $token = $_BODY["token"];

        $token_secret = $secret;
        $result = Token::validate($token, $token_secret);

        // if the token is valid, 
        if($result)
        {
            // Gets the user_id stored in the payload of the token
            $payload = Token::getPayload($token);
            $user_id = $payload["user_id"];

            $user = new UserController();
            $user = $user->setUserCharacteristicsByUserId($ability_max, $ability_current, $life_max, $life_current, $chance_max, $chance_current, $magic_max, $magic_current, $user_id);
            echo json_encode(["message" => "User characteristics updated"]);

        }
        // else, the token is not valid
        else
        {
            echo json_encode(["message" => "Invalid token"]);
            http_response_code(401);
        }
    }
    // else, data about the user and or token are missing
    else
    {
        echo json_encode(["message" => "Must provide token and user data"]);
        http_response_code(400);
    }
}

// Sets current step of a user by it's user_id / PUT METHOD ========================================================================
// ==================================================================================================================================
if ($_SERVER['REQUEST_URI'] == "/user/set_step" and $_SERVER["REQUEST_METHOD"] == "PUT")
{

    // Gets the body of the request, and turns the received json into an associative array
    $input = file_get_contents("php://input");
	$_BODY = json_decode($input, true);

    // If the token and the needed data to set the user step are provided
    if( isset($_BODY["token"]) and
    isset($_BODY["step"])
    )
    {
        // Get the user step from the body of the request
        $step = $_BODY["step"];

        // Gets the token from the body of the request and the secret key from the env file.
        // Checks if the token is valid.
        $token = $_BODY["token"];

        $token_secret = $secret;
        $result = Token::validate($token, $token_secret);

        // if the token is valid
        if($result)
        {
            $payload = Token::getPayload($token);
            $user_id = $payload["user_id"];

            $user = new UserController();
            $user = $user->setUserStepByUserId($step, $user_id);
            echo json_encode(["message" => "step updated"]);
        }
        // else the token  is not valid
        else
        {
            echo json_encode(["message" => "Invalid token"]);
            http_response_code(401);
        }
    }
    // else, data about the uset step and or token are missing
    else
    {
        echo json_encode(["message" => "Must provide token and step data"]);
        http_response_code(400);
    }
}


