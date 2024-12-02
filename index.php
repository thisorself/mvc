<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="utf-8">
    <title>MVC</title>
</head>

<body>

    <?php
    include 'Controller.php';

    if (!isset($_COOKIE['token'])) {
        print '<h4>Незареестрований гiсть!</h4>
               <form action="register.php" method="POST">
               <p>Введiть нiкнейм:</p><input type="text" name="username">
               <p>Введiть пароль:</p><input type="text" name="password"><br>
               <input type="submit" value="Зарееструватися"></button>';
    }

    ?>

</body>

</html>