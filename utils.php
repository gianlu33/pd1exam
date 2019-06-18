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

?>