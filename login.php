<?php 
session_start();
include 'utils.php';

if(isset($_SESSION["email"])){
    myRedirect("main.php");
}

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
        <h3 id="subtitle">Login</h3>
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
    	
    	<div class="error_message">
    	<span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
    	Errore al login!
    	</div>
      
        <div class="form">
            <fieldset>
                <legend>Inserisci le tue credenziali</legend><br>
                Email:<br>
                <input type="email" name="email" id="email"><br>
                Password:<br>
                <input type="password" name="password" id="psw"><br>
                <input type="submit" value="Entra" onclick="login()">
            </fieldset>
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