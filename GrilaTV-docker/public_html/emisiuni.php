<?php require_once('include.php') ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=1080, initial-scale=1.0">
    <title>Hello, world!</title>
</head>
<body>
    <h1>Salut. Bine ați venit pe prima pagină.</h1>
    <h3>Acest serviciu este menit să ofere clienților programul TV pentru anumite posturi.</h3>
    <div style="font-size:14pt;">
    <ul>
        <li><a style="font-size:16pt;" href="http://<?php echo $_SERVER['SERVER_NAME'];?>"><i>Întoarce-te acasă</i></a></li>
    </ul>
    <?php 
        if($connected) {
            if(isset($_GET['p']) && !empty($_GET['p'])) {
                $posttv = $_GET['p'];
                print("<p><i>Emisiunile ce rulează pe $posttv sunt: </i></p>");
                $sql = "SELECT posturitv.Denumire as PostTV, emisiuni.Denumire AS Emisiune, emisiuni.Tip AS Generic, programe.* FROM difuzari 
                INNER JOIN emisiuni ON difuzari.Emisiune=emisiuni.ID
                INNER JOIN programe ON difuzari.Program=programe.ID
                INNER JOIN posturitv ON difuzari.Post=posturitv.Numar
                WHERE posturitv.Denumire=?;";
                $stmt = $conn->prepare($sql);
                print("$conn->error");
                $stmt->bind_param('s', $posttv);
            } else {
                $sql = "SELECT posturitv.Denumire as PostTV, emisiuni.Denumire AS Emisiune, emisiuni.Tip AS Generic, programe.* FROM difuzari 
                INNER JOIN emisiuni ON difuzari.Emisiune=emisiuni.ID
                INNER JOIN programe ON difuzari.Program=programe.ID
                INNER JOIN posturitv ON difuzari.Post=posturitv.Numar;";
                $stmt = $conn->prepare($sql);
            }
            if($conn->begin_transaction(MYSQLI_TRANS_START_READ_ONLY)) {
                if($stmt->execute()) {
                    if(($result = $stmt->get_result()) && $conn->commit()) {
                        if($result) {
                            $row = $result->fetch_assoc();
                            if($row!=null) {
                                print("<table>");
                                print("<tr>");
                                foreach ($row as $collumn => $value) {
                                    print("<th>$collumn</th>");
                                }
                                print("</tr>");
                                do {
                                    print("<tr>");
                                    foreach ($row as $collumn => $value) {
                                        if($collumn == "Denumire")
                                            print("<td><a href=\"emisiuni.php?p=$value\">$value</a></td>");
                                        else print("<td>$value</td>");
                                    }
                                    print("</tr>");
                                } while($row = $result->fetch_assoc());
                                print("</table>");
                            } else print("Nu am rezultate.");
                        } else print("Eroare la preluarea datelor: $conn->error");
                    } else print("Eroare la commit: $conn->error");
                } else {
                    print("Eroare la executie: $conn->error");
                    $conn->rollback();
                }
            }
        }
    ?>
    </div>
</body>
</html>