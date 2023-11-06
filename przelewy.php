<?php
// Połączenie z bazą danych MySQL
$servername = "";
$username = "";
$password = "";
$dbname = "";

$conn = new mysqli($db_host, $db_user, $db_password, $db_database);

// Sprawdzenie połączenia
if ($conn->connect_error) {
    die('<p style="color: black">Błąd połączenia z bazą danych: </p>' . $conn->connect_error);
}

session_start();
// Obsługa operacji bankowych
$imie = $_SESSION['imie'];
$nazwisko = $_SESSION['nazwisko'];
$email = $_SESSION['email'];
$id2 = $_SESSION['id'];
$saldo = $_SESSION['saldo'];

var_dump($_POST);

if (isset($_SESSION['imie'])) {
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $akcja = $_POST["akcja"];
    $kwota = $_POST["kwota"];
    $id = $_POST["id"];

    echo '<div style="color: black;">';
    echo "Imię: $imie<br>";
    echo "Nazwisko: $nazwisko<br>";
    echo "Email: $email<br>";
    echo "ID: $id2<br>";
    echo "Saldo: $saldo<br>";
    echo '</div>';
    
    if ($akcja == "przelew") {
        if ($kwota > 0) {
        // Rozpocznij transakcję
        $conn->begin_transaction();

        // Realizuj przelew z jednego konta na drugie
        $sql1 = "UPDATE user SET saldo = saldo - $kwota WHERE id = $id2";
        $sql2 = "UPDATE user SET saldo = saldo + $kwota WHERE id = $id";
        $result = $conn->query($sql2);

        if ($conn->query($sql1) === TRUE && $conn->query($sql2) === TRUE) {
            if ($conn->affected_rows > 0) {
            // Zatwierdź transakcję
            $conn->commit();
            echo '<p style="color: black">Przelew został zrealizowany.</p>';
            } else {
                $conn->rollback();
                echo "Podane ID nie istnieje w bazie danych. Spróbuj ponownie.";
            }
        } else {
            // Wycofaj transakcję w przypadku błędu
            $conn->rollback();
            die('<p style="color: black">Wystąpił błąd systemu.</p>');
        }
    } else {
        echo "Kwota nie może być mniejsza niż 0";
    }
    }
}
} else {
    header("Location: logowanie.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>MTBank - Przelewy</title>
    <meta charset="UTF-8">
</head>
<body>
    <h1>Przelewy</h1>
    <form method="post" action="przelewy.php">
        <label for="id">ID:</label>
        <input type="number" name="id" id="id" placeholder="ID"><br><br>
        <label for="kwota">Kwota:</label>
        <input type="number" name="kwota" id="kwota" placeholder="Kwota"><br><br>
        <button type="submit" name="akcja" value="przelew">Przelej pieniądze</button>
    </form>
</body>
</html>

