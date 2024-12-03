<?php
include 'core/connection.php';
include 'models/User.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = User::isExists($pdo);

    if ($user) {
        print "Успішна реєстрація!<br>" . $user->fields->fullname . " вітаємо!<br>";
        print "<a href='estate.php?mode=read&real_estate=0'>Перейти до перегляду нерухомостi.</a>";
    } else {
        print "Реєстрація не вдалася!<br>Невірний логін або пароль!";
    }
}

?>