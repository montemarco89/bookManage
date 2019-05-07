<?php

// connessione al DB 
$conn = new mysqli('localhost', 'root', '', 'utenti');
$conn2 = new mysqli('localhost', 'root', '', 'biblioteca');

// controllo la connessione 
if($conn === false){
    die("ERRORE: non si è riuscito a connettersi al Database. " . $conn->connect_error);
}

if($conn2 === false){
    die("ERRORE: non si è riuscito a connettersi al Database. " . $conn2->connect_error);
}

?>