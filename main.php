<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8" />
    <title>Noleggio mezzi</title>
    <script charset="utf-8" src="js/jquery-3.4.1.min.js"></script>
    <link rel="stylesheet" href="css/app.css" /> 
    
    <?php include 'utils.php' ?>
</head>
<body>
    <header>
        <h1 id="title">Noleggio mezzi di trasporto</h1>
        <h3 id="subtitle">Prenotazioni</h3>
    </header>
    
    <nav class="js_view">
    	<a href="main.php">Prenotazioni<br></a>
    	<a id="menu_login" href="login.php">Login<br></a>
    	<a id="menu_registration" href="registration.php">Registrazione<br></a>
    	<a id="menu_logout" href="main.php">Logout</a>
    </nav>
    
    <?php fillMap() ?>
    
    <noscript>
    	<h2>Javascript disabilitato.</h2>
    	<style>
            .js_view {display:none;}
        </style>
    </noscript>
    
    <script charset="utf-8" src="js/app.js"></script>
</body>
</html>