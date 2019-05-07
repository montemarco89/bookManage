<?php

session_start();

// controllo se l'utente è loggato, se non è loggato lo reindirizzo nella pagina di login
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

require_once "config.php";

// definisco le variabili
$new_pwd = $confirm_pwd = "";
$new_pwd_err = $confirm_pwd_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    // convalido la nuova password
    if(empty(trim($_POST["new_pwd"]))){
        $new_pwd_err = "Inserisci la password.";
    } elseif(strlen(trim($_POST["new_pwd"])) < 6){
        $new_pwd_err = "La password deve essere almeno di 6 caratteri.";
    } else{
        $new_pwd = trim($_POST["new_pwd"]);
    }

    // convalido la conferma della nuova password
    if(empty(trim($_POST["confirm_pwd"]))){
        $confirm_pwd_err = "Perfavore conferma la password";
    } else{
        $confirm_pwd = trim($_POST["confirm_pwd"]);
        if(empty($new_pwd_err) && ($new_pwd != $confirm_pwd)){
            $confirm_pwd_err = "Le password non coincidono";
        }
    }

    //inserisco la nuova password nel database
    if(empty($new_pwd_err) && empty($confirm_pwd_err)){

        if($stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?")){
            $stmt->bind_param("si", $param_password, $param_id);

            $param_password = password_hash($new_pwd, PASSWORD_DEFAULT);
            $param_id = $_SESSION["id"];

            if($stmt->execute()){

                session_destroy();
                header("location : login.php");
                exit();
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
    <title>Reimposta la password</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font:14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px }
    </style>
    <script src="script.js"></script>
</head>
<body>
    <div class="wrapper">
        <h2>Reimposta la password</h2>
        <p>Riempi questo form per reimpostare la password</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        
            <div class="form-group <?php echo (!empty($new_pwd_err)) ? 'has-error' : ''; ?>">
                <label>Nuova password</label>
                <input type="password" name="new_pwd" class="form-control" id="myPassword" value="<?php echo $new_pwd; ?>">
                <span class="help-block"><?php echo $new_pwd_err; ?></span>
                <input type="checkbox" onclick="showPassword()">Mostra la password
            </div>

            <div class="form-group <?php echo (!empty($confirm_pwd_err)) ? 'has-error' : ''; ?>">
                <label>Conferma la password</label>
                <input type="password" name="confirm_pwd" class="form-control" id="myPassword">
                <span class="help-block"><?php echo $confirm_pwd_err; ?></span>
                
            </div>
        
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <a class="btn btn-link" href="welcome.php">Cancella</a>
            </div>
        </form>
    </div>
</body>
</html>
