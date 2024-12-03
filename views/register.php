<?php


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = User::isExists($pdo);

    if ($user) {

    } else {
        print "Реєстрація не вдалася!<br>Невірний логін або пароль!";
    }
}

?>