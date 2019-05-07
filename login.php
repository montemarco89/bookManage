<?php

session_start();

// vedo se l'utente è già loggato, se sì lo rinvio sulla welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: index.php");
    exit;
}

require_once "config.php";

$username = $password = "";
$user_error = $pwd_error = "";

// processo i dati del form dopo l'invio
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // controllo se il campo username è vuoto
    if(empty(trim($_POST["username"]))){
        $user_error = "Inserisci il nome utente.";
    } else {
        $username = trim($_POST["username"]);
    }

    // controllo se il campo password è vuoto
    if(empty(trim($_POST["password"]))){
        $pwd_error = "Inserisci la tua password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // validazione delle credenziali
    if(empty($user_error) && empty($pwd_error)){
        
        if($stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?")){
            
            // prendo l'username dal form
            $stmt->bind_param("s", $param_username);
            $param_username = $username;
            
            if($stmt->execute()){
                
                // mi salvo i risultati della query
                $stmt->store_result();
                
                // se lo statement restituisce una riga l'utente esiste
                if($stmt->num_rows == 1){
                    
                     // prendo i risultati delle variabili id, username e password hashata
                    $stmt->bind_result($id, $username, $hashed_password);
                    
                    // prendo i dati dello statement
                    if($stmt->fetch()){
                        
                        // controllo se la password è verificata
                        if(password_verify($password, $hashed_password)){
                            
                            // password corretta, inizio una nuova sessione
                            session_start();
                            
                            // variabili di sessione
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            
                            // redirect alla welcome page
                            header("location: index.php");
                        } else{
                            // errore se la password è errata
                            $pwd_error = "La password inserita non è corretta.";
                        } 
                    }
                 
                } else{
                // errore se l'username non esiste
                $user_error = "Nessun account trovato per quell'utente.";
                }                       
            
            } else{
                    
                echo "Qualcosa è andato storto. Riprova più tardi.";
            }
        }    
            
        $stmt->close();    
    } 

    $conn->close();    
}    

?>



<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
    <script src="script.js"></script>
</head>
<body>
    <div class="wrapper">
        <h2>Login</h2>
        <p>Completa i campi con le tue credenziali.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?> " method="post">
            
            <div class="form-group <?php echo (!empty($user_error)) ? 'has-error' : ''; ?>">
                <label>Username</label>
                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>" >
                <span class="help-block"><?php echo $user_error; ?></span>
            </div>
            
            <div class="form-group <?php echo (!empty($pwd_error)) ? 'has-error' : ''; ?>">
                <label>Password</label>
                <input type="password" name="password" class="form-control" id="myPassword">
                <span class="help-block"><?php echo $pwd_error; ?></span>
                <input type="checkbox" onclick="showPassword()">&nbsp;Mostra la password
            </div>
            
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Entra">
            </div>
            <p>Non hai un account?<a href="register.php">&nbsp;Iscriviti ora</a>.</p>
        </form>
    </div>
</body>
</html>