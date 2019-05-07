<?php
// includo il file di configurazione 
require_once "config.php";

// definisco le variabili e le inizializzo vuote
$username = $password = $email =""; 
$user_error = $pwd_error = $email_error = "";
$confirm_pwd_err = $confirm_pwd = "";
 
// processo i dati al submit 
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // valido l'username
    if(empty(trim($_POST["username"]))){
        $user_error = "Inserisci un nome utente valido.";
    } else {
        // preparo una select per controllare se l'utente è già esistente
        if($stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ?")) {
            // faccio un binding sulle variabili
            $stmt->bind_param("s", $param_username);

            // setto i parametri
            $param_username = trim($_POST["username"]);

            // provo ad eseguire lo statement
            if($stmt->execute()){
                
                $stmt->store_result();
                
                // controllo se l'utente è già presente
                if($stmt->num_rows == 1){
                    $user_error = "Questo username è già usato. Inserirne un altro.";
                } else {
                    $username = trim($_POST["username"]);
                }
            } else {
                echo "Qualcosa è andato storto! Riprova più tardi.";
            }
        }
        // chiudo lo statement
        $stmt->close();
    }

    // convalido la password
    if(empty(trim($_POST["password"]))){
        $pwd_error = "Inserisci una password.";
    } elseif(strlen(trim($_POST["password"])) < 6){
        $pwd_error = "La password deve avere minimo 6 caratteri.";
    } else {
        $password = trim($_POST["password"]);
    }

    // convalido la email
    
    if(empty(trim($_POST["email"]))){
        $email_error = "Inserisci l'email.";
    } else {
        
        // preparo lo statement per controllare se l'utente è già presente
        if($stmt = $mysqli->prepare("SELECT id FROM users WHERE email = ?")) {
            
            // faccio un binding sulle variabili
            $stmt->bind_param("s", $param_email);

            // setto i parametri
            $param_email = trim($_POST["email"]);

            // provo ad eseguire lo statement
            if($stmt->execute()){
            
                $stmt->store_result();
                
                // controllo se l'email è già presente
                if($stmt->num_rows == 1){
                    $email_error = "Questa email è già stata usata. Inserirne un'altra.";
                } else {
                    $email = trim($_POST["email"]);
                }
            } else {
                echo "Qualcosa è andato storto! Riprova più tardi.";
            } 
        }
        // chiudo lo statement
        $stmt->close();
    }
   
    // chiedo la conferma della validazione
    if(empty(trim($_POST["confirm_pwd"]))){
        $confirm_pwd_err = "Conferma la password.";
    } else{
        $confirm_pwd = trim($_POST["confirm_pwd"]);
        if(empty($pwd_error) && ($password != $confirm_pwd)){
            $confirm_pwd_err = "Le password non corrispondono. Ridigitare.";
        }
    }

    // valido l'inserimento nel database dei dati
    if(empty($user_error) && empty($pwd_error) && empty($confirm_pwd_err)){
        
        // preparo l'inserimento nel database dell'utente    
        if($stmt = $mysqli->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)")) {
            // binding dei parametri
            $stmt->bind_param("sss", $param_username, $param_password, $param_email);
            // setto i parametri
            $param_username = $username;
            // creo un hash per la password
            $param_password = password_hash($password, PASSWORD_DEFAULT);
            $param_email = $email;

            if($stmt->execute()){
                // redirect alla pagina di login
                header("location: login.php");
            } else{
                echo "Qualcosa è andato storto. Riprova più tardi.";
            }

        }
        //chiudo lo statement
        $stmt->close();
    }    
    
    // chiudo la connessione
    $mysqli->close();

}

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>REGISTRATI</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
    <script src="script.js"></script>
</head>
<body>
    <div class="wrapper">
        <h2>Registrati<h2>
        <p>Compila il form per creare un account.<p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method = "post">
        
            <div class="form-group <?php echo (!empty($email_error)) ? 'has-error' : '';?>">
                <label>E-Mail</label>
                <input type="email" name="email" class="form-control" value="<?php echo $email; ?>">
                <span class="help-block">
                    <?php echo $email_error; ?>
                </span>
            </div>
            
            <div class="form-group <?php echo (!empty($user_error)) ? 'has-error' : ''; ?>">
                <label>Username</label>
                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                <span class="help-block">
                    <?php echo $user_error; ?>
                </span>
            </div>
            
            <div class="form-group <?php echo (!empty($pwd_error)) ? 'has-error' : ''; ?>">
                <label>Password</label>
                <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
                <span class="help-block">
                    <?php echo $pwd_error; ?>
                </span>
                <input type="checkbox" onclick="showPassword()">Mostra la password
            </div>
            
            <div class="form-group <?php echo (!empty($confirm_pwd_err)) ? 'has-error' : ''; ?>">
                <label>Conferma la password</label>
                <input type="password" name="confirm_pwd" class="form-control" value="<?php echo $confirm_pwd; ?>">
                <span class="help-block">
                    <?php echo $confirm_pwd_err; ?>
                </span>
            </div>
            
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Invia">
                <input type="reset" class="btn btn-default" value="Reset">
            </div>
                <p>Hai già un account? <a href="login.php">Entra</a>
        </form>
    </div>
</body>
</html>
