<?php 
    session_start();
    include 'utils.php';
    checkHTTPS();
    if(isset($_SESSION["email"]) && verifyInactivity()) {
        $_REQUEST['type_msg'] = "warning_message";
        $_REQUEST['message'] = "Sessione scaduta.";
    }
?>

<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boom Bici</title>
    <script charset="utf-8" src="js/jquery-3.4.1.min.js"></script>
    <script charset="utf-8" src="js/app.js"></script>
    <link rel="stylesheet" href="css/app.css" /> 
</head>
<body>
    <header>
    	<h1 id="title">Prenotazioni</h1>
    	<a href="main.php"><img id="logo" src="./img/logo_dark.png"></a>
    </header>
    
    <div id="background"></div>
    
    <div id="left_block" class="js_view">
        <nav class="js_view">
        	<a href="main.php">Prenotazioni<br></a>
        	<?php if(!isset($_SESSION["email"])) { ?>
        	<a id="menu_login" href="login.php">Login<br></a>
        	<a id="menu_registration" href="registration.php">Registrazione<br></a>
        	<?php } else { ?>
        	<a id="menu_logout" onclick="logout()">Logout</a>
        	<?php } ?>
   		</nav>
    </div>
    
    <div id="center_block" class="js_view"> 
    
        <div id="info_message">
            <p class="p_inline"></p>
            <span class="closebtn" onclick="hideMsg()">&times;</span>
    	</div>       
    	 
        <div id="tot_disp">
            Disponibilit&agrave totale:
            Motorini: <p class="p_inline" id="tot_moto"></p>
            Biciclette: <p class="p_inline" id="tot_bici"></p>
        </div>
        
    	<?php fillMap(); ?>
    </div>
    
    <div id="right_block" class="js_view">         	
    	<div id="point_infos">
    		<h3 style="margin-top: 20px">Posto di noleggio selezionato</h3>
    		<p id="disp_posto"></p>
    		
    		<?php if(isset($_SESSION["email"])) { ?>
    		<form id="reservation">
                <fieldset>
                    <legend>Prenota</legend><br>
                    Numero motorini:<br>
                    <!-- TODO vedi se mettere tendina -->
                    <input id="prenota_moto" type="number" name="moto" min="0"><br>
                    Numero biciclette:<br>
                    <input id="prenota_bici" type="number" name="bici" min="0"><br>
                    <input class="button_2" type="submit" value="Prenota" onclick="reserve(event)">
                </fieldset>
       		</form>
       		<?php } ?>
       		
    	</div>
    </div>
    	     
    <noscript>
    	<h2>Javascript disabilitato.</h2>
    	<style>
            .js_view {display:none;}
        </style>
    </noscript>
    
    <?php 
        if(isset($_REQUEST["type_msg"]) && isset($_REQUEST["message"])) {
            $type = $_REQUEST['type_msg'];
            $msg = $_REQUEST['message'];
            echo "<script>showMsg('$type', '$msg') </script>";
         } 
     ?>
        
</body>
</html>