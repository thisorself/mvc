<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="utf-8">
    <title>MVC</title>
</head>

<body>

    <?php
    include 'core/connection.php';
    include 'models/User.php';
    include 'Controller.php';

    $fields = ['4', 'new_usrname', 'new_psswrd', 'new_Testov Test Testovich', "new_+0", "new_@g", "realtor"];
    $user = User::create($pdo, $fields);
    if ($user) {
        /*print $user->fields->username;
        print $user->fields->password;
        print $user->fields->fullname;
        print $user->fields->phone;
        print $user->fields->email;
        print $user->fields->role;*/
        print $user->update();
    }

    /*
    if (!isset($_COOKIE['token'])) {
        print '<h4>Незареестрований гiсть!</h4>
               <form action="register.php" method="POST">
               <p>Введiть нiкнейм:</p><input type="text" name="username">
               <p>Введiть пароль:</p><input type="text" name="password"><br>
               <input type="submit" value="Зарееструватися"></button>';
    }*/

    ?>

</body>

</html>