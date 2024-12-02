<?php
include 'connection.php';
include 'User.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = isset($_POST['username']) ? $_POST['username'] : "";
    $password = isset($_POST['password']) ? $_POST['password'] : "";

    $user = User::findByUsername($pdo, $username);

    if ($user && password_verify($password, $user->password)) {
        print "Успішна реєстрація!<br>" . $user->fullname . " вітаємо!<br>";
        setcookie('token', $user->password, time() + 2000);
        print "<a href='estate.php?mode=read&real_estate=0'>Перейти до перегляду нерухомостi.</a>";
        exit();
    } else {
        print "Реєстрація не вдалася!<br>Невірний логін або пароль!";
    }
}

?>