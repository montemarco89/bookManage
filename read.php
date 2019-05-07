<?php

// controlliamo l'esistenza di almeno un elemento prima di continuare
if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
    
    require_once "config.php";

    // preparo lo statement
    if($stmt = $conn2->prepare("SELECT * FROM libri WHERE ID = ?")){
        $stmt->bind_param("i", $param_id);
        $param_id = trim($_GET["id"]);

        // eseguo la query
        if($stmt->execute()){
            $result = $stmt->get_result();

            // se ho risultati li inserisco nelle variabili
            if($result->num_rows == 1){
            
                $row = $result->fetch_array(MYSQLI_ASSOC);

                $titolo = $row["Titolo"];
                $cognome = $row["Autore_Cognome"];
                $nome = $row["Autore_Nome"];
                $anno = $row["Anno"];
                $pagine = $row["Pagine"];
                $genere = $row["Genere"];
            } else{
            
            // se non ci sono valori vado alla pagina di errore
            header("location: error.php"); 
            exit();
            }
        
        } else{
        echo "Qualcosa è andato storto. Riprova tra poco.";
        }
    }
    $stmt->close();
    $conn2->close();

} else{
    
    header("location: error.php");
    exit();
}     

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Guarda i libri</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; text-align: center; }
        .wrapper{ width: 500px; margin: 0 auto}
        table tr td:last-child a{ margin-right: 15px; }
        th{ text-align: center;}
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h1>Consulta i libri presenti</h1>
                    </div>
                    <table class="table table-bordered table-stripped">
                        <thead>
                            <tr>
                                <th>Titolo</th>
                                <th>Autore Cognome</th>
                                <th>Autore Nome</th>
                                <th>Anno</th>
                                <th>Pagine</th>
                                <th>Genere</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php echo $row["Titolo"]; ?></td>
                                <td><?php echo $row["Autore_Cognome"]; ?></td>
                                <td><?php echo $row["Autore_Nome"]; ?></td>
                                <td><?php echo $row["Anno"]; ?></td>
                                <td><?php echo $row["Pagine"]; ?></td>
                                <td><?php echo $row["Genere"]; ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <p><a href="index.php" class="btn btn-primary">Indietro</a></p>
                </div>
            </div>   
        </div>
    </div>   
</body>
</html>
