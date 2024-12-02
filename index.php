<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="utf-8">
    <title>MVC</title>
</head>

<body>

    <?php
    include 'config.php';
    include 'User.php';
    include 'RealEstate.php';
    function showRealEstates()
    {
        include 'config.php';
        $count_estates = count(RealEstate::all($pdo));

        if ($_GET["real_estate"] != 0)
            print '<a href="index.php?real_estate=' . $_GET["real_estate"] - 1 . '">Назад</a> ';
        if ($_GET["real_estate"] + 1 != $count_estates)
            print ' <a href="index.php?real_estate=' . $_GET["real_estate"] + 1 . '">Далi</a><br>';

        print '<a href="forms/add.php">Додати</a> ';
        print ' <a href="forms/edit.php?index=0">Редагувати</a>';
        print '<br><a href="index.php?logout=true">Вийти</a>';
    }

    if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
        setcookie('token', '', time() - 3600);
        header('Location: index.php');
        exit;
    }

    if (!isset($_COOKIE['token'])) {
        print '<h4>Незареестрований гiсть!</h4>
               <form action="register.php" method="POST">
               <p>Введiть нiкнейм:</p><input type="text" name="username">
               <p>Введiть пароль:</p><input type="text" name="password"><br>
               <input type="submit" value="Зарееструватися"></button>';
    } else
        showRealEstates();
    ?>

</body>

</html>