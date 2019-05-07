<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>Ricerca autori</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
<style type="text/css">
    body{ font: 14px sans-serif; text-align: left; }
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
    <h1>Inserisci il cognome di un autore/un'autrice da cercare nello spazio sottostante</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
        <input type="search" name="search">&nbsp;<input type="submit" name="button" value="Cerca"></p>
    </form>
    <div>
    
    <?php
    
    require_once "config.php";

        if($conn2 === false){
            die("ERRORE: impossibile connetersi. " . $conn2->connect_error);
        }

        $search = $search_error = "";

        if($_SERVER["REQUEST_METHOD"] == "POST"){
            
            $input_search = trim($_POST["search"]);

            if(empty($input_search)){
                $search_error = "Inserisci un titolo.";
            } else {
                $search = $input_search;
            }
            
            if(empty($search_error)){
                
                $sql = "SELECT * FROM libri WHERE autore_cognome LIKE '%$search%'";
                
                if($result = $conn2->query($sql)){
                    if($result->num_rows > 0){
                        echo "<table class='table table-bordered table-stripped'>";
                                echo "<thead>";
                                    echo "<tr>";
                                        echo "<th>#</th>";
                                        echo "<th>Titolo</th>";
                                        echo "<th>Autore</th>";
                                        echo "<th>Azioni</th>";
                                    echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                        
                        while($row = $result->fetch_array()){
                            echo "<tr>";
                                echo "<td>" . $row['ID'] . "</td>";
                                echo "<td>" . $row['Titolo'] . "</td>";
                                echo "<td>" . $row['Autore_Cognome'] . "</td>"; 
                                echo "<td>";

                                // inserisco l'icona occhio per vedere il record
                                echo "<a href='read.php?id=" . $row['ID'] ."' title='View Record' data-toggle='tooltip'>
                                <span class='glyphicon glyphicon-eye-open'></span></a>";
                                //inserisco l'icona matita per modificarlo
                                echo "<a href='update.php?id=" . $row['ID'] ."' title='Update Record' data-toggle='tooltip'>
                                <span class='glyphicon glyphicon-pencil'></span></a>";
                                // inserisco l'icona bidone per eliminare il record
                                echo "<a href='delete.php?id=" . $row['ID'] ."' title='Delete Record' data-toggle='tooltip'>
                                <span class='glyphicon glyphicon-trash'></span></a>";

                                echo "</td>";
                            echo "</tr>";
                        }
                        echo "</tbody>";
                    echo "</table>";
                    $result->free();
                    } else{
                        echo "<p class='lead'><em>Non sono stati trovati elementi.</em></p>";
                    }
                } else{
                echo "Errore. Impossibile eseguire la ricerca $sql. " . $conn2->error;
                }
            
            
            }
            
        }

        $conn2->close();

        ?>
    </div>
    <br>
    <div>
        <a href="index.php" class="btn btn-default">Torna alla Home</a>
    </div>
</body>
</html>




    