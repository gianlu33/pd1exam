<?php
session_start();

include 'utils.php';

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
    
    $x = htmlentities($_REQUEST["x"]);
    $y = htmlentities($_REQUEST["y"]);
        
    try {
        if(!ctype_digit($x) || !ctype_digit($y))
            throw new Exception("Parametri non validi");
        
        $result = getInfoPoint($x, $y);
        echo $result;
    }
    catch(Exception $e) {
        http_response_code (400);
        echo $e->getMessage();
    }
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
    
    $email = htmlentities($_REQUEST["email"]);
    
    //no strip of psw. (md5 later will remove any problem.)
    $psw = $_REQUEST["psw"];
        
    try {
        //TODO vedi se lasciarli.. perchè è anche abbastanza inutile
        if(!checkEmail($email))
            throw new Exception("Email non valida");
            
        if(!checkPassword($psw))
            throw new Exception("Password non valida");
                
        authLogin($email, $psw);    
        $_SESSION["email"] = $email;
        $_SESSION['time']=time();
    }
    catch(Exception $e) {
        http_response_code(400);
        echo $e->getMessage();
    }
}

function manageLogout() {
    if(!isset($_SESSION["email"])){
        http_response_code (401);
        exit;
    }
    
    destroySession();
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
    
    $email = htmlentities($_REQUEST["email"]);
    //no strip of psw. (md5 later will remove any problem.)
    $psw = $_REQUEST["psw"];
    $psw_confirm = $_REQUEST["psw_confirm"];
           
    try {
        if(!checkEmail($email)) 
            throw new Exception("Email non valida");
        
        if(!checkPassword($psw))
            throw new Exception("Password non valida");
        
        if($psw !== $psw_confirm)
            throw new Exception("I due campi della password non sono uguali");
            
        authRegistration($email, $psw);
        $_SESSION["email"] = $email;
        $_SESSION['time']=time();
    }
    catch(Exception $e) {
        http_response_code (400);
        echo $e->getMessage();
    }
}

function manageReservation() {    
    if(!isset($_SESSION["email"])){
        http_response_code (401);
        echo "Non sei loggato";
        exit;
    }
    
    if(verifyInactivity()) {
        http_response_code (401);
        echo "Sessione scaduta";
        exit;
    }
    
    if(!isset($_REQUEST["x"]) || !isset($_REQUEST["y"]) || 
       !isset($_REQUEST["moto"]) || !isset($_REQUEST["bici"])){
        http_response_code (400);
        echo "Parametri mancanti";
        exit;
    }
    
    $x = htmlentities($_REQUEST["x"]);
    $y = htmlentities($_REQUEST["y"]);
    $moto = htmlentities($_REQUEST["moto"]);
    $bici = htmlentities($_REQUEST["bici"]);
        
    try {
        //non accetto valori negativi! il ctype_digit controlla se ogni singolo carattere sia un numero
        if(!ctype_digit($x) || !ctype_digit($y) || !ctype_digit($moto) || !ctype_digit($bici))
            throw new Exception("Parametri non validi");
        
        if($moto == 0 && $bici == 0)
            throw new Exception("Devi prenotare almeno un mezzo");
        
        $result = authReservation($x, $y, $moto, $bici);
        echo $result;
    }
    catch(Exception $e) {
        http_response_code (400);
        echo $e->getMessage();
    }
}

?>