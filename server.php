<?php
session_start();

include 'utils.php';

//TODO sessione.. ste cose solo se sei loggato le puoi fare
//TODO vedi se va bene che accedendo dal browser mi restituisce err..

if(!isset($_REQUEST["type"])) {
    http_response_code (400);
    exit;
}

switch($_REQUEST["type"]) {
    case "infopoint":
        manageInfoPoint();
        break;
        
    case "login":
        manageLogin();
        break;
        
    case "logout":
        manageLogout();
        break;
        
    case "registration":
        manageRegistration();
        break;
        
    case "reservation":
        manageReservation();
        break;
        
    default:
        http_response_code (400);
        break;
}

exit;

function manageInfoPoint() {   
    if(!isset($_REQUEST["x"]) || !isset($_REQUEST["y"])){
        http_response_code (400);
        exit;
    }
    
    $x = $_REQUEST["x"];
    $y = $_REQUEST["y"];

    echo getInfoPoint($x, $y);
}

function manageLogin() {
    if(isset($_SESSION["email"])){
        http_response_code (401);
        exit;
    }
    
    if(!isset($_REQUEST["email"]) || !isset($_REQUEST["psw"])){
        http_response_code (400);
        exit;
    }
    
    $email = $_REQUEST["email"];
    $psw = $_REQUEST["psw"];
    
    $result = authLogin($email, $psw);    
    
    if($result === true){
        $_SESSION["email"] = $email;
    }
    else {
        http_response_code (401);
    }
}

function manageLogout() {
    if(!isset($_SESSION["email"])){
        http_response_code (401);
        exit;
    }
    
    session_destroy();
}

function manageRegistration() {
    if(isset($_SESSION["email"])){
        http_response_code (401);
        exit;
    }
    
    if(!isset($_REQUEST["email"]) || !isset($_REQUEST["psw"]) || !isset($_REQUEST["psw_confirm"])){
        http_response_code (400);
        exit;
    }
    
    $email = $_REQUEST["email"];
    $psw = $_REQUEST["psw"];
    $psw_confirm = $_REQUEST["psw_confirm"];
    
    //TODO controlla uguaglianza psw
    
    $result = authRegistration($email, $psw);
    
    if($result === true){
        $_SESSION["email"] = $email;
    }
    else {
        http_response_code (401);
    }
}

function manageReservation() {
    if(!isset($_SESSION["email"])){
        http_response_code (401);
        exit;
    }
    
    if(!isset($_REQUEST["x"]) || !isset($_REQUEST["y"]) || 
       !isset($_REQUEST["moto"]) || !isset($_REQUEST["bici"])){
        http_response_code (400);
        exit;
    }
    
    $x = $_REQUEST["x"];
    $y = $_REQUEST["y"];
    $moto = $_REQUEST["moto"];
    $bici = $_REQUEST["bici"];
    
    //TODO controlla qui?
    
    echo authReservation($x, $y, $moto, $bici);    
}

?>