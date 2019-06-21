<?php

//check if user wants to access this page directly. if yes -> error
if(strpos($_SERVER["REQUEST_URI"], 'utils.php')){
    http_response_code (401);
    exit;
}

function fillMap(){
    $width = 600;
    $height = 400;
    
    echo "<svg class='js_view' width='$width' height='$height' id='svg_container'>\n";
    echo "<rect width='600' height='400' id='map' />\n";
    
    getAvailability($height);
    
    echo "Sorry, your browser does not support inline SVG.\n";
    echo "</svg>\n";
}

function getAvailability($height){
    $tot_bici = 0;
    $tot_moto = 0;
    
    $conn = dbConnect();
    
    $sql = "SELECT * FROM AVAILABILITY";
    
    if(! $risposta = mysqli_query($conn,$sql)) { 
        myRedirect("main.php", "Errore di collegamento al DB"); 
    }
    
    while($row = mysqli_fetch_array($risposta, MYSQLI_ASSOC)) {
        drawCircle($height, $row["X"], $row["Y"], $row["NumBici"] + $row["NumMoto"]);
        
        $tot_moto += (int) $row["NumMoto"];
        $tot_bici += (int) $row["NumBici"];
    }
    
    //show total
    echo "<script>	
            $('#tot_moto').text($tot_moto);
	        $('#tot_bici').text($tot_bici);
	       </script>";
     
    mysqli_free_result($risposta);
    mysqli_close($conn);
}

function dbConnect() {
    $user = "root";
    $pwd = "";
    $db = "exam";
    
    $conn = mysqli_connect("localhost", $user, $pwd, $db);
    if(mysqli_connect_error()) { 
        throw new Exception("Errore di collegamento al DB");
    }
    
    return $conn;
}

function drawCircle($height, $x, $y, $num){ 
    $real_y = $height - $y;
    
    if($num >= 4)
        $class = "green";
    else if($num < 4 && $num > 0)
        $class = "yellow";
    else
        $class = "red";
    
    echo "<circle id='circle_". $x . "_" . $y . "' cx='$x' cy='$real_y' r='2.5' class=$class
            onclick='displayInfoPoint($x, $y)' />\n";
}

function console_log( $data ){
    echo '<script>';
    echo "console.log('$data')";
    echo '</script>';
}

function getInfoPoint($x, $y){
    $conn = dbConnect();
    
    $x = mysqli_real_escape_string($conn, $x);
    $y = mysqli_real_escape_string($conn, $y);
    
    $sql = "SELECT SUM(NumMoto), SUM(NumBici) FROM AVAILABILITY WHERE X = $x AND Y = $y";
    
    if(! $risposta = mysqli_query($conn,$sql)) {
        throw new Exception("Errore durante la query al DB");
    }
    
    $row = mysqli_fetch_array($risposta, MYSQLI_NUM);
    
    if(!isset($row[0]) || !isset($row[1])) {
        throw new Exception("Posto di noleggio non esistente");
    }
    
    $availability = $row[0] . "-" . $row[1];
    
    mysqli_free_result($risposta);
    mysqli_close($conn);
    
    return $availability;
}

function authLogin($username, $password) {
    $conn = dbConnect();
        
    $username = mysqli_real_escape_string($conn, $username);
    $psw_md5 = md5($password);
    
    //TODO vedi se fare prima una select per vedere se username esiste (per differenziare i msg errore)
    
    $sql = "SELECT COUNT(*) FROM USERS WHERE Username = '$username' AND Password = '$psw_md5'";
    
    if(!$risposta = mysqli_query($conn,$sql)) {
        throw new Exception("Errore durante la query al DB");
    }
    
    $row = mysqli_fetch_array($risposta, MYSQLI_NUM);

    mysqli_free_result($risposta);
    mysqli_close($conn);
    
    if($row[0] == 0) throw new Exception("Username o password errati.");
}

function authRegistration($username, $password) {
    $conn = dbConnect();
    
    $username = mysqli_real_escape_string($conn, $username);
    $psw_md5 = md5($password);
    //TODO valuta se mettere sale
    
    //TODO vedi se fare prima una select per vedere se username esiste gia
    
    $sql = "INSERT INTO USERS(Username, Password) VALUES ('$username', '$psw_md5')";
    
    if(!mysqli_query($conn,$sql)) {
        mysqli_close($conn);
        throw new Exception("Username gi" . utf8_encode("à") . " esistente");
    }
    
    mysqli_close($conn);
}

function authReservation($x, $y, $moto, $bici) {
    $conn = dbConnect();
    
    $user = $_SESSION["email"];
    
    $x = mysqli_real_escape_string($conn, $x);
    $y = mysqli_real_escape_string($conn, $y);
    $moto = mysqli_real_escape_string($conn, $moto);
    $bici = mysqli_real_escape_string($conn, $bici);
    
    try {
        mysqli_autocommit($conn,false);
        
        $sql_check = "SELECT NumMoto, NumBici FROM AVAILABILITY WHERE X = '$x' AND Y = '$y' FOR UPDATE";
        
        if(! $risposta = mysqli_query($conn,$sql_check)) {
            throw new Exception("Errore verifica disponibilit" . utf8_encode("à") . " posti");
        }        
        
        $row = mysqli_fetch_array($risposta, MYSQLI_ASSOC);
        
        if(!isset($row["NumMoto"]) || !isset($row["NumBici"])) {
            throw new Exception("Posto di noleggio non esistente");
        }
        
        $max_moto = $row["NumMoto"];
        $max_bici = $row["NumBici"];
        mysqli_free_result($risposta);
        
        if($moto <= $max_moto && $bici <= $max_bici) {
            $moto_sold = $moto;
            $bici_sold = $bici;
        }
        else if($moto > $max_moto && $max_bici >= ($moto - $max_moto) + $bici) {
            $moto_sold = $max_moto;
            $bici_sold = $bici + $moto - $max_moto;
        }
        else {
            throw new Exception("Disponibilit" . utf8_encode("à") . " insufficiente.");
        }
        
        $sold = "Assegnati: $moto_sold motorini, $bici_sold biciclette";
        
        $sql_reserve = "UPDATE AVAILABILITY SET NumMoto = NumMoto - '$moto_sold', 
                        NumBici = NumBici - '$bici_sold' WHERE X = '$x' AND Y = '$y'";
        
        if(!mysqli_query($conn,$sql_reserve)) {
            throw new Exception("Errore aggiornamento posti in AVAILABILITY");
        }
        
        $sql_insert = "INSERT INTO RESERVATIONS (Username, X, Y, NumBici, NumMoto)
                        VALUES ('$user', '$x', '$y', '$bici_sold', '$moto_sold')";
        
        if(!mysqli_query($conn,$sql_insert)) {
            throw new Exception("Errore aggiunta riga nella tabella RESERVATIONS");
        }
        
        if(!mysqli_commit($conn)) {
            throw new Exception("Errore al commit");
        }
        
        mysqli_autocommit($conn,true);
        mysqli_close($conn);
        return $sold;
    
    } catch(Exception $e) {
        mysqli_rollback($conn);
        mysqli_autocommit($conn,true);
        mysqli_close($conn);
        throw new Exception($e->getMessage());
    }
}

function checkEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function checkPassword($password) {
    $regex = "/^([a-zA-Z\d]*[^a-zA-Z\d]){2,}[a-zA-Z\d]*$/";
    
    return preg_match($regex, $password);
}

function verifyInactivity() {
    $t=time(); $diff=0; $new=false;
    
    if (isset($_SESSION['time'])){
        $t0=$_SESSION['time']; 
        $diff=($t-$t0); // inactivity
    } else {
        $new=true;
    }
    
    if ($new || ($diff > 120)) { // new or with inactivity period too long
        destroySession();
        return true;
    } else {
        $_SESSION['time']=time(); /* update time */
        return false;
    }
}

function destroySession() {
    $_SESSION=array();
    // If it's desired to kill the session, also delete the session cookie.
    // Note: This will destroy the session, and not just the session data!
    if (ini_get("session.use_cookies")) { // PHP using cookies to handle session
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 3600*24, $params["path"],
            $params["domain"], $params["secure"], $params["httponly"]);
    }
    session_destroy(); // destroy session
}

function myRedirect($url="main.php") {
    header('HTTP/1.1 307 temporary redirect');
    // L’URL relativo è accettato solo da HTTP/1.1
    header("Location: $url");
    exit;
}

function checkLogged() {
    if(!verifyInactivity()){
        myRedirect();
    }
}

function checkHTTPS() {
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        // La richiesta e' stata fatta su HTTPS
    } else {
        // Redirect su HTTPS
        // eventuale distruzione sessione e cookie relativo
        $redirect = 'https://' . $_SERVER['HTTP_HOST'] .
        $_SERVER['REQUEST_URI'];
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $redirect);
        exit;
    }
}

?>