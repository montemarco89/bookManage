<?php

require_once "config.php";

// definisco le variabili
$titolo = $autore_cognome = $autore_nome = $anno = $pagine = $genere = "";
$titolo_err = $cognome_err = $nome_err = $anno_err = $pagine_err = $genere_err = "";

// processo il form al submit
if(isset($_POST["id"]) && !empty($_POST["id"])){
    // recupero l'id "nascosto"
    $id = $_POST["id"];

    // valido il titolo
    $input_titolo = trim($_POST["titolo"]);
    if(empty($input_titolo)){
        $titolo_err = "Inserisci un titolo di un libro.";
    } else{
        $titolo = $input_titolo;
    }

    // valido l'autore
    $input_cognome = trim($_POST["cognome"]);
    if(empty($input_cognome)){
        $cognome_err = "Inserisci il cognome dell'autore.";
    } else{
        $autore_cognome = $input_cognome;
    }

    $input_nome = trim($_POST["nome"]);
    if(empty($input_nome)){
        $nome_err = "Inserisci il nome dell'autore.";
    } else{
        $autore_nome = $input_nome;
    }

    // valido l'anno di pubblicazione
    $input_anno = trim($_POST["anno"]);
    $min=1000;
    $max=date('Y');
    
    if(empty($input_anno)){
        $anno_err = "Inserisci l'anno di pubblicazione.";
    } elseif(filter_var($input_anno, FILTER_VALIDATE_INT, array("options"=>array("min_range"=>$min, "max_range"=>$max))) === false){
        $anno_error = "Inserisci un giusto anno di pubblicazione.";
    } else {
        $anno = $input_anno;
    }

    // valido il numero di pagine
    $input_pagine = trim($_POST["pagine"]);
    if(empty($input_pagine)){
        $pagine_err = "Inserisci il numero di pagine.";    
    } elseif(!ctype_digit($input_pagine)){
        $pagine_err = "Inserisci un numero corretto di pagine.";
    } else{
        $pagine = $input_pagine;
    }

    // convalido il genere
    $input_genere = trim($_POST["genere"]);
    if(empty($input_genere)){
        $genere_err = "Inserisci il genere del libro";
    } else{
        $genere = $input_genere;
    }

    // controllo se ci sono errori nei campi prima di inserirli nel database
    if(empty($titolo_err) && empty($cognome_err) && empty($nome_err) && empty($anno_err)
        && empty($pagine_err) && empty($genere_err)){

            //preparo lo statement di update
            if($stmt = $conn2->prepare("UPDATE libri SET titolo=?, autore_cognome=?, autore_nome=?, anno=?, 
            pagine=?, genere=? WHERE id=?")){
                
                // faccio il biding sulle variabili come parametri
                $stmt->bind_param("sssiisi", $param_titolo, $param_cognome, $param_nome, $param_anno, $param_pagine, 
                $param_genere, $param_id);

                //setto i parametri
                $param_titolo = $titolo;
                $param_cognome = $autore_cognome;
                $param_nome = $autore_nome;
                $param_anno = $anno;
                $param_pagine = $pagine;
                $param_genere = $genere;
                $param_id = $id;

                // eseguo lo statement
                if($stmt->execute()){
                    
                    // se i record vengono aggiornati l'utente viene reindirizzato sulla home
                    header("location: index.php");
                    exit();
                } else{
                    echo "Qualcosa è andato storto. Riprova più tardi.";
                }
                $stmt->close();    
            }
            $conn2->close();    
        }
               
} else{
    // controllo se è presente quell'id prima di altri processi
    if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){

        //prendo l'id
        $id = trim($_GET["id"]);

        // preparo una select su quell'id
        if($stmt = $conn2->prepare("SELECT * FROM libri WHERE id = ?")){
            $stmt->bind_param("i", $param_id);
            $param_id = $id;

            // provo ad eseguire lo statement
            if($stmt->execute()){
                // mi salvo i record che mi restituisce la query
                $result = $stmt->get_result();

                if($result->num_rows == 1){
                    // se il risultato contiene una riga lavoro sui campi
                    $row = $result->fetch_array(MYSQLI_ASSOC);

                    // mi salvo i valori dei campi nelle variabili
                    $titolo = $row["Titolo"];
                    $autore_cognome = $row["Autore_Cognome"];
                    $autore_nome = $row["Autore_Nome"];
                    $anno = $row["Anno"];
                    $pagine = $row["Pagine"];
                    $genere = $row["Genere"];
                } else{
                    // se non è presente un id valido l'utente verrà rimandato nella pagina di errore
                    header("location: error.php");
                    exit();
                }
            } else{
                echo "Qualcosa è andato storto. Riprova più tardi.";
            }
        }
        $stmt->close();
        $conn2->close();
    
    } else{
        // non c'è l'id, redirect alla pagina di errore
        header("location: error.php");
        exit();
    }    
}

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Aggiorna e Modifica</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        .wrapper{ width: 500px; margin: 0 auto;}
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h2>Modifica il libro</h2>
                    </div>
                    <p>Modifica i valori e clicca "Modifica" per rendere effettivi i cambiamenti.</p>
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
                        <!-- Titolo -->
                        <div class="form-group <?php echo (!empty($titolo_err)) ? 'has-error' : ''; ?>">
                            <label>Titolo</label>
                            <input type="text" name="titolo" class="form-control" value="<?php echo $titolo; ?>">
                            <span class="help-block"><?php echo $titolo_err;?></span>
                        </div>
                        <!-- Autore -->
                        <div class="form-group <?php echo (!empty($cognome_err)) ? 'has-error' : ''; ?>">
                            <label>Autore Cognome</label>
                            <input type="text" name="cognome" class="form-control" value="<?php echo $autore_cognome; ?>">
                            <span class="help-block"><?php echo $cognome_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($nome_err)) ? 'has-error' : ''; ?>">
                            <label>Autore Nome</label>
                            <input type="text" name="nome" class="form-control" value="<?php echo $autore_nome; ?>">
                            <span class="help-block"><?php echo $nome_err;?></span>
                        </div>
                        <!-- Anno -->
                        <div class="form-group <?php echo (!empty($anno_err)) ? 'has-error' : ''; ?>">
                            <label>Anno di Pubblicazione</label>
                            <input type="text" name="anno" class="form-control" value="<?php echo $anno; ?>">
                            <span class="help-block"><?php echo $anno_err;?></span>
                        </div>
                        <!-- Pagine -->
                        <div class="form-group <?php echo (!empty($pagine_err)) ? 'has-error' : ''; ?>">
                            <label>Pagine</label>
                            <input type="text" name="pagine" class="form-control" value="<?php echo $pagine; ?>">
                            <span class="help-block"><?php echo $pagine_err;?></span>
                        </div>
                        <!-- Genere -->
                        <div class="form-group <?php echo (!empty($genere_err)) ? 'has-error' : ''; ?>">
                            <label>Genere</label>
                            <input type="text" name="genere" class="form-control" value="<?php echo $genere; ?>">
                            <span class="help-block"><?php echo $genere_err;?></span>
                        </div>

                        <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                        <input type="submit" class="btn btn-primary" value="Modifica">
                        <a href="index.php" class="btn btn-default">Cancella</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>




