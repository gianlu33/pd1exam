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
        <h3 id="subtitle">Registrazione</h3>
        
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
    
        <div class="error_message" id="error_message">
        <p class="p_inline"></p>
        <span class="closebtn" onclick="$(this).parent().hide(200)">&times;</span>
    	</div>
		
		<div class="form">
            <fieldset>
                <legend>Inserisci i tuoi dati</legend><br>
                Email:<br>
                <input type="email" name="email" id="email" placeholder="bob@gmail.com"><br>
                Password:<br>
                <input type="password" name="password" id="psw"><br>
                Conferma password:<br>
                <input type="password" name="password_confirm" id="psw_confirm"><br>
                <ul>
                <li style="text-align: left">La password deve contenere almeno due caratteri speciali</li>
                </ul>
                <input type="submit" value="Registrati" onclick="registration()">
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