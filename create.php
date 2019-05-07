<?php

// includo il file di configurazione della connessione al DB
require_once "config.php";

// definisco le variabili. Al momento vuote
$titolo = $autore_cogn = $autore_nome = $anno = $pagine = $genere ="";
// definisco anche le variabili d'errore, sempre vuote 
$titolo_error = $cogn_error = $nome_error = $anno_error = $pagine_error = $genere_error ="";

// processo i dati del form al momento del "submit"
if($_SERVER["REQUEST_METHOD"] === "POST"){
    
    // convalido il titolo
    $input_title = trim($_POST["titolo"]);
    
    // se il campo è vuoto
    if(empty($input_title)){
        $titolo_error = "Inserisci un titolo";
    } else{
        $titolo = $input_title;
    }

    // convalido l'autore
    $input_cognome = trim($_POST["autore_cognome"]);
    if(empty($input_cognome)){
        $cogn_error = "Inserisci un autore";
    } else{
        $autore_cogn = $input_cognome;
    }

    $input_nome = trim($_POST["autore_nome"]);
    if(empty($input_nome)){
        $nome_error = "Inserisci un nome";
    } else{
        $autore_nome = $input_nome;
    }

    // convalido l'anno
    $input_anno = trim($_POST["anno"]);
    $min=1000;
    $max=date('Y');
    
    if(empty($input_anno)){
        $anno_error = "Inserisci un anno di pubblicazione";
    } elseif(filter_var($input_anno, FILTER_VALIDATE_INT, array("options"=>array("min_range"=>$min, "max_range"=>$max))) === false){
        $anno_error = "Inserisci un giusto anno di pubblicazione";
    } else {
        $anno = $input_anno;
    }

    // convalido le pagine
    $input_pagine = trim($_POST["pagine"]);
    if(empty($input_pagine)){
        $pagine_error = "Inserisci il numero di pagine";
    } else{
        $pagine = $input_pagine;
    }

    // convalido il genere
    $input_genere = trim($_POST["genere"]);
    if(empty($input_genere)){
        $genere_error = "Inserisci il genere del libro";
    } else{
        $genere = $input_genere;
    }

    // controllo se ci sono stati degli errori di input 
    if(empty($titolo_error) && empty($cogn_error) && empty($nome_error) && empty($anno_error)
        && empty($pagine_error) && empty($genere_error)){

        // preparo la stmt$stmt di inserimento nel DB
        if($stmt = $conn2->prepare("INSERT INTO libri (titolo, autore_cognome, autore_nome, anno, pagine, genere) 
            VALUES (?, ?, ?, ?, ?, ?)")){

            // binding dei parametri
            $stmt->bind_param("sssiis", $param_titolo, $param_cognome, $param_nome, $param_anno, $param_pagine, $param_genere);

            // assegno i valori alle mie variabili
            $param_titolo = $titolo;
            $param_cognome = $autore_cogn;
            $param_nome = $autore_nome;
            $param_anno = $anno;
            $param_pagine = $pagine;
            $param_genere = $genere;

            // eseguo la stmt$stmt
            if($stmt->execute()){

                // se il record è stato creato con successo vengo reindirizzato alla pagina iniziale
                header("location:index.php");
                exit();
            
            } else{
                echo "Qualcosa è andato storto. Riprova più tardi.";
            }
        // chiudo la stmt$stmt
        $stmt->close(); 
        }      

    }
    
    // chiudo la connessione al DB
    $conn2->close();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Crea Libri</title>
    <style type="text/css">
        .wrapper-c{ width:500px; margin: 0 auto}
    </style>
</head>
<body>
    <div class="wrapper-c">
        <div class="container-flud">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h2>Inserisci un libro</h2>
                    </div>
                    <p>Riempi i campi sottostanti ed aggiungi un libro al database</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group <?php echo (!empty($titolo_error)) ? 'has-error' : ''; ?>">
                            <label>Titolo</label>
                            <input type="text" name="titolo" class="form-control" value="<?php echo $titolo; ?>">
                            <span class="help-block"><?php echo $titolo_error;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($cogn_error)) ? 'has-error' : ''; ?>">
                            <label>Autore Cognome</label>
                            <input type="text" name="autore_cognome" class="form-control" value="<?php echo $autore_cogn; ?>">
                            <span class="help-block"><?php echo $cogn_error;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($nome_error)) ? 'has-error' : ''; ?>">
                            <label>Autore Nome</label>
                            <input type="text" name="autore_nome" class="form-control" value="<?php echo $autore_nome; ?>">
                            <span class="help-block"><?php echo $nome_error;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($anno)) ? 'has-error' : ''; ?>">
                            <label>Anno</label>
                            <input type="number" name="anno" class="form-control" value="<?php echo $anno; ?>">
                            <span class="help-block"><?php echo $anno_error;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($pagine)) ? 'has-error' : ''; ?>">
                            <label>Pagine</label>
                            <input type="number" name="pagine" class="form-control" value="<?php echo $pagine; ?>">
                            <span class="help-block"><?php echo $pagine_error;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($genere)) ? 'has-error' : ''; ?>">
                            <label>Genere</label>
                            <input type="text" name="genere" class="form-control" value="<?php echo $genere; ?>">
                            <span class="help-block"><?php echo $genere_error;?></span>
                        </div>
                        <input type="submit" class="btn btn-primary" value="Crea">
                        <a href="index.php" class="btn btn-default">Indietro</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>    