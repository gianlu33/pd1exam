<?php 
    session_start();
    include 'utils.php';
    checkHTTPS();
    checkLogged();
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
    	<h1 id="title">Login</h1>
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
    	      
        <form>
            <fieldset>
                <legend>Inserisci le tue credenziali</legend><br>
                Email:<br>
                <input type="email" name="email" id="email" placeholder="bob@gmail.com"><br>
                Password:<br>
                <input type="password" name="password" id="psw"><br>
                <input class="button_1" type="submit" value="Entra" onclick="login(event)">
            </fieldset>
        </form>
        
    </div>
    
    <div id="ck_js_disabled" class="error_message">
        <p class="p_inline">Javascript disabilitato.</p>
	</div>   
    	
    <noscript>
    	<style>
            .js_view {display:none;}
            #ck_js_disabled {display: block;}
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