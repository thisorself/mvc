<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="utf-8">
    <title>MVC</title>
</head>

<body>

    <?php
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

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        include 'config.php';

        $username = isset($_POST['username']) ? $_POST['username'] : "";
        $password = isset($_POST['password']) ? $_POST['password'] : "";

        $user = User::findByUsername($pdo,$username);

        if ($user && password_verify($password, $user->password)) {
            echo "Успішна реєстрація!<br>" . $user->fullname . " вітаємо!";
            setcookie('token', $user->password, time() + 200);
            header('Location: index.php?real_estate=0');
            exit();
        } else {
            echo "Реєстрація не вдалася!<br>Невірний логін або пароль!";
        }
    }

    if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
        setcookie('token', '', time() - 3600);
        header('Location: index.php');
        exit;
    }

    if (isset($_COOKIE['token']) && isset($_GET['real_estate'])) {
        include 'config.php';

        $query = $pdo->prepare("SELECT * FROM real_estates");
        $query->execute();
        $rows = $query->fetchAll(PDO::FETCH_OBJ);

        $i = $_GET['real_estate'];
        print $i;

        print "<h3>" . $rows[$i]->property_type . ", " . $rows[$i]->deal_type . "</h3><br>";
        print "<h4><i>" . $rows[$i]->address . "</i><h4><br>";
        print $rows[$i]->price . "<br>";
    }

    if (!isset($_COOKIE['token'])) {
        print '<h4>Незареестрований гiсть!</h4>
               <form action="index.php" method="POST">
               <p>Введiть нiкнейм:</p><input type="text" name="username">
               <p>Введiть пароль:</p><input type="text" name="password"><br>
               <input type="submit" value="Зарееструватися"></button>';
    } else showRealEstates();
    ?>

</body>

</html>