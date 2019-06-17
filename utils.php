<?php

function fillMap(){
    $width = 600;
    $height = 400;
    
    echo "<svg class='js_view' width='$width' height='$height' id='svg_container'>";
    echo "<rect width='600' height='400' id='map' />";
    
    //TODO connessione al DB per le postazioni noleggio
    //TODO rimuovi questi due cerchi
    echo "<circle id='circle_1' cx='50' cy='50' r='5' fill='red' stroke='black' stroke-width='0' 
            onclick=displayInfoPoint(1) />";
    echo "<circle id='circle_2' cx='200' cy='300' r='5' fill='red' stroke='black' stroke-width='0'
            onclick=displayInfoPoint(2) />";
    
    echo "Sorry, your browser does not support inline SVG.";
    echo "</svg>";
}



?>