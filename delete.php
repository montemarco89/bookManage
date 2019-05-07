<?php

if(isset($_POST["id"]) && !empty($_POST["id"])){

    require_once "config.php";

    // preparo lo statement di cancellazione
    if($stmt = $conn2->prepare("DELETE FROM libri WHERE id=?")){

        //prendo l'id 
        $stmt->bind_param("i", $param_id);

        // setto l'id trovato
        $param_id = trim($_POST["id"]);

        // provo ad eseguire lo statement di cancellazione
        if($stmt->execute()){

            // se i record vengono cancellati correttamente reindirizzo verso la pagina iniziale
            header("location: index.php");
            exit();
        } else{
            
            // se i record non vengono cancellati o c'è un errore stampo un messaggio a video
            echo "Qualcosa è andato storto. Riprova più tardi.";
        }
    }
    $stmt->close();
    $conn2->close();
} else{

    // controllo l'esistenza dell'id se non c'è
    if(empty(trim($_GET["id"]))){

        // se non esiste l'id, reindirizzo alla pagina di errore
        header("location: error.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Elenco Libri</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        .wrapper{ width: 500px; margin: 0 auto}
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h1>Cancella i libri</h1>
                    </div>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="alert alert-danger fade in">
                            <input type="hidden" name="id" value="<?php echo trim($_GET["id"]); ?>"/>
                            <p>Sei sicuro di voler eliminare questo libro?</p><br>
                            <p>
                                <input type="submit" value="Sì" class="btn btn-danger">
                                <a href="index.php" class="btn btn-default">No</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>