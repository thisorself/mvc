<?php
include 'core/connection.php';
include 'models/User.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = User::isExists($pdo);

    if ($user) {
        print "Успішна реєстрація!<br>" . $user->fields->fullname . " вітаємо!<br>";
        print "<a href='views/estate.php?mode=read&real_estate=1'>Перейти до перегляду нерухомостi.</a><br>";
        print "<a href='views/user.php?mode=read&real_estate=1'>Перейти до перегляду користувачiв.</a>";
    } else {
        print "Реєстрація не вдалася!<br>Невірний логін або пароль!";
    }
}

?>