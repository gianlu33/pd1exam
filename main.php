<?php 
session_start();
include 'utils.php';
?>

<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Noleggio mezzi</title>
    <script charset="utf-8" src="js/jquery-3.4.1.min.js"></script>
    <link rel="stylesheet" href="css/app.css" /> 
</head>
<body>
    <header>
        <h1 id="title">Noleggio mezzi di trasporto</h1>
        <h3 id="subtitle">Prenotazioni</h3>
        <?php if(isset($_REQUEST["reservation_result"]) && isset($_REQUEST["reservation_message"])) { ?>
        <div class="<?php echo $_REQUEST["reservation_result"] ?>" style="display:block">
          <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
          <?php echo $_REQUEST["reservation_message"] ?>
        </div>
        <?php } ?>
    </header>
    
    <div id="left_block" class="js_view">
        <nav class="js_view">
        	<a href="main.php">Prenotazioni<br></a>
        	<?php if(!isset($_SESSION["email"])) { ?>
        	<a id="menu_login" href="login.php">Login<br></a>
        	<a id="menu_registration" href="registration.php">Registrazione<br></a>
        	<?php } else { ?>
        	<a id="menu_logout" href="main.php" onclick="logout()">Logout</a>
        	<?php } ?>
   		</nav>
    </div>
    
    <div id="center_block" class="js_view">
        <div id="tot_disp">
            Disponibilit&agrave totale:
            Motorini: <p class="p_inline" id="tot_moto"></p>
            Biciclette: <p class="p_inline" id="tot_bici"></p>
        </div>
    	<?php fillMap(); ?>
    </div>
    
    <div id="right_block" class="js_view">         	
    	<div id="point_infos">
    		<h3>Posto di noleggio selezionato:</h3>
    		<p id="disp_posto"></p>
    		
    		<?php if(isset($_SESSION["email"])) { ?>
    		<div class="form" id="reservation">
                <fieldset>
                    <legend>Prenota</legend><br>
                    Numero motorini:<br>
                    <input id="prenota_moto" type="number" name="moto" min="0"><br>
                    Numero biciclette:<br>
                    <input id="prenota_bici" type="number" name="bici" min="0"><br>
                    <input type="submit" value="Prenota" onclick="reserve()">
                </fieldset>
       		</div>
       		<?php } ?>
       		
    	</div>
    </div>
    	     
    <noscript>
    	<h2>Javascript disabilitato.</h2>
    	<style>
            .js_view {display:none;}
        </style>
    </noscript>
    
    <script charset="utf-8" src="js/app.js"></script>
</body>
</html>