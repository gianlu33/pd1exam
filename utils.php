<?php

function fillMap(){
    $width = 600;
    $height = 400;
    
    echo "<svg class='js_view' width='$width' height='$height' id='svg_container'>\n";
    echo "<rect width='600' height='400' id='map' />\n";
    
    getAvailability();
    
    echo "Sorry, your browser does not support inline SVG.\n";
    echo "</svg>\n";
}

function getAvailability(){
    $tot_bici = 0;
    $tot_moto = 0;
    
    $conn = dbConnect();
    
    $sql = "SELECT * FROM AVAILABILITY";
    
    if(! $risposta = mysqli_query($conn,$sql)) { 
        myRedirect("main.php", "Errore di collegamento al DB"); 
    }
    
    while($row = mysqli_fetch_array($risposta, MYSQLI_ASSOC)) {
        drawCircle($row["X"], $row["Y"], $row["NumBici"] + $row["NumMoto"]);
        
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
    //TODO mettere exceptions se va male la connessione, e catturarle in giro facendo cose ragionevoli
    $user = "root";
    $pwd = "";
    $db = "exam";
    
    $conn = mysqli_connect("localhost", $user, $pwd, $db);
    if(mysqli_connect_error()) { 
        myRedirect("main.php", "Errore di collegamento al DB"); 
    }
    
    return $conn;
}

//TODO rivedi
function myRedirect($location, $msg="") {
    header('HTTP/1.1 307 temporary redirect');
    // L’URL relativo è accettato solo da HTTP/1.1
    header("Location: $location?msg=".urlencode($msg));
    exit; // Necessario per evitare ulteriore
    // processamento della pagina
}

function drawCircle($x, $y, $num){        
    if($num >= 4)
        $class = "green";
    else if($num < 4 && $num > 0)
        $class = "yellow";
    else
        $class = "red";
    
    echo "<circle id='circle_". $x . "_" . $y . "' cx='$x' cy='$y' r='5' class=$class
            onclick='displayInfoPoint($x, $y)' />\n";
}

function console_log( $data ){
    echo '<script>';
    echo "console.log('$data')";
    echo '</script>';
}

function getInfoPoint($x, $y){
    $conn = dbConnect();
    
    //TODO sanificare, statement
    $sql = "SELECT SUM(NumMoto), SUM(NumBici) FROM AVAILABILITY WHERE X = $x AND Y = $y";
    
    if(! $risposta = mysqli_query($conn,$sql)) {
        //TODO vedi questo caso
        myRedirect("main.php", "Errore di collegamento al DB");
    }
    
    $row = mysqli_fetch_array($risposta, MYSQLI_NUM);
    
    $availability = $row[0] . "-" . $row[1];
    
    mysqli_free_result($risposta);
    mysqli_close($conn);
    
    return $availability;
}

function authLogin($username, $password) {
    $conn = dbConnect();
        
    //TODO sanificare, statement
    $psw_md5 = md5($password);
    
    $sql = "SELECT COUNT(*) FROM USERS WHERE Username = '$username' AND Password = '$psw_md5'";
    
    if(! $risposta = mysqli_query($conn,$sql)) {
        //TODO vedi questo caso
        myRedirect("login.php", "Errore di collegamento al DB");
    }
    
    $row = mysqli_fetch_array($risposta, MYSQLI_NUM);

    if($row[0] == 1) $returnValue = true;
    else $returnValue = false;
    
    mysqli_free_result($risposta);
    mysqli_close($conn);
    
    return $returnValue;
}

function authRegistration($username, $password) {
    $conn = dbConnect();
    $returnValue = true;
    
    //TODO sanificare, statement
    $psw_md5 = md5($password);
    
    $sql = "INSERT INTO USERS(Username, Password) VALUES ('$username', '$psw_md5')";
    
    if(!mysqli_query($conn,$sql)) {
        //TODO vedi questo caso
        $returnValue = false;
    }
    
    mysqli_close($conn);
    return $returnValue;
}

function authReservation($x, $y, $moto, $bici) {
    $conn = dbConnect();
    mysqli_autocommit($conn,false);
    
    $user = $_SESSION["email"];
    
    //TODO sanificare, statement
    
    try {
        $sql_check = "SELECT NumMoto, NumBici FROM AVAILABILITY WHERE X = '$x' AND Y = '$y'";
        
        if(! $risposta = mysqli_query($conn,$sql_check)) {
            //TODO vedi questo caso
            throw new Exception("Errore verifica disponibilità posti");
        }
        
        $row = mysqli_fetch_array($risposta, MYSQLI_ASSOC);
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
            //TODO vedi questo caso
            throw new Exception("Errore aggiornamento posti disponibili");
        }
        
        $sql_insert = "INSERT INTO RESERVATIONS (Username, X, Y, NumBici, NumMoto)
                        VALUES ('$user', '$x', '$y', '$bici_sold', '$moto_sold')";
        
        if(!mysqli_query($conn,$sql_insert)) {
            //TODO vedi questo caso
            throw new Exception("Errore aggiunta riga nella tabella RESERVATIONS");
        }
        
        if(!mysqli_commit($conn)) {
            throw new Exception("Errore al commit");
        }
    
    }catch(Exception $e) {
        mysqli_rollback($conn);
        mysqli_autocommit($conn,true);
        mysqli_close($conn);
        return "0-" . $e->getMessage();
    }
    
    mysqli_autocommit($conn,true);
    mysqli_close($conn);
    return "1-$sold";
}
?>