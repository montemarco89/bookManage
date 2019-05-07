<?php

session_start();

// controllo se l'utente è loggato altrimenti lo indirizzo alla pagina di login

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){

    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Benvenuto</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; text-align: center; }
        .wrapper-w{ width: 650px; margin: 0 auto; }
        .page-header h2{ margin-top: 0 }
        table tr td:last-child a{ margin-right: 15px }
    </style>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/boostrap.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();
    
        });
    </script> 
</head>
<body>
    <div class="page-header">
        <h1>Ciao, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Benvenuto nel nostro sito.</h1>
    </div>
    <p>
        <!-- collegamento al reset della password -->
        <a href="reset.php" class="btn btn-warning">Reimposta la tua password.</a>
        <!-- disconnessione dal sito -->
        <a href="logout.php" class="btn btn-danger">Disconettiti dal tuo account</a>
    </p>
    <div class="wrapper">
        <div class="container-flud">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header clearfix">
                        <h2 class="pull-left">Ultimi libri inseriti</h2>
                        <!-- riferimento alla pagina di ricerca di un libro -->
                        <a href="search_title.php" class="btn btn-info">Cerca un libro</a>
                        <!-- riferimento alla pagina di ricerca autore -->
                        <a href="search_author.php" class="btn btn-info">Cerca un autore</a>
                        <!-- riferimento alla pagina di creazione di un nuovo record -->
                        <a href="create.php" class="btn btn-success pull-right">Inserisci un nuovo libro</a>
                    </div>
                    
                    <?php
                    // riferimento al file di connessione al database
                    require_once "config.php";

                    // creo la tabella con i libri presenti
                    
                    // eseguo la query sulla tabella libri
                    if($result = $conn2->query("SELECT * from libri ORDER BY Data_Inserimento DESC")){
                        // se sono presenti record mi stampa la tabella 
                        if($result->num_rows > 0){
                            // creo la struttura della tabella
                            echo "<table class='table table-bordered table-stripped'>";
                                echo "<thead>";
                                    echo "<tr>";
                                        echo "<th>#</th>";
                                        echo "<th>Titolo</th>";
                                        echo "<th>Autore</th>";
                                    echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                
                                //riempio la tabella
                                
                                // per ogni record trovato stampo le righe della tabella
                                while($row = $result->fetch_array()){
                                    echo "<tr>";
                                        echo "<td>" . $row['ID'] . "</td>";
                                        echo "<td>" . $row['Titolo'] . "</td>";
                                        echo "<td>" . $row['Autore_Cognome'] . "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody>";
                            echo "</table>";
                            // libero l'array di risultati
                            $result->free();
                    } else{
                        // se non sono presenti record stampo un messaggio
                        echo "<p class='lead'><em>Non sono stati trovati elementi.</em></p>";
                    }
                } else{
                    // se non riesco a connettermi al databse stampo il messaggio di errore
                    echo "Errore. Impossibile eseguire $sql." . $conn2->error;
                }

                $conn2->close();
                ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>