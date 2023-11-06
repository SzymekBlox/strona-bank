<?php

$db_host = "";
$db_user = "";
$db_password = "";
$db_database = "";

session_start();

if (isset($_REQUEST['action']) && $_REQUEST['action'] == "login") {
    $email = $_REQUEST['email'];
    $password = $_REQUEST['password'];

    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    //obiektowo
    $db = mysqli_connect($db_host, $db_user, $db_password, $db_database);
    //var_dump($db);

    //strukturalnie 
    //$d = mysqli_connect("localhost", "root", "", "auth");
    //mysqli_query($d, "SELECT * FROM user");


    //ręcznie:
    //$q = "SELECT * FROM user WHERE email = '$email'";
    //echo $q;
    //$db->query($q);

    //prepared statements
    $q = $db->prepare("SELECT * FROM user WHERE email = ? LIMIT 1");
    //podstaw wartości
    $q->bind_param("s", $email);
    //wykonaj
    $q->execute();
    $result = $q->get_result();

    //wiadomosci
    $text1 = "Zalogowano poprawnie<br>";
    $text3 = "Błędny login lub hasło <br>";

    $userRow = $result->fetch_assoc();
    if ($userRow == null) {
        //konto nie istnieje
        echo '<p style="color: white">' . $text3 . '</p>';
    } else {
        //konto istnieje
        if (password_verify($password, $userRow['passwordHash'])) {
            //hasło poprawne
            $_SESSION['imie'] = $userRow['imie'];
            $_SESSION['nazwisko'] = $userRow['nazwisko'];
            $_SESSION['email'] = $userRow['email'];
            $_SESSION['id'] = $userRow['id'];
            $_SESSION['saldo'] = $userRow['saldo'];
            $imie = $_SESSION['imie'];
            $nazwisko = $_SESSION['nazwisko'];
            $email = $_SESSION['email'];
            $id = $_SESSION['id'];
            $saldo = $_SESSION['saldo'];
            
            header("Location: profil.php");
        } else {
            //hasło niepoprawne
            echo '<p style="color: white">' . $text3 . '</p>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>MtBank - Logowanie</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="logowanie.css">
    <link rel="icon" href="img/logo2.png" type="image/x-icon">
</head>
<body>
    <div class="container">
        <div class="login-form">
            <div class="login-title">
                Logowanie
            </div>
            <form>
                <div class="login-input">
                    <label class="login-input-info" for="emailInput">Email:</label>
                    <input type="email" name="email" id="emailInput" required>
                </div>    
                <div class="login-input">
                    <label class="login-input-info" for="passwordInput">Hasło:</label>
                    <input type="password" name="password" id="passwordInput" required>
                </div>
                <input type="hidden" name="action" value="login">
                <input class="btn" type="submit" value="Zaloguj">
                <p>Nie masz konta? <a href="rejestracja.php">Zarejestruj się</a></p>
            </form>
        </div>
    </div>
</body>
</html>