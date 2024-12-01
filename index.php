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

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
        $estates = RealEstate::all($pdo);

        $i = $_GET['real_estate']; 

        print '<form action="save.php" method="POST">
                <p>Номер нерухомостi: ' . $estates[$i]->id . '</p>
                <p>Мiсце знаходження: <input type="text" name="location" value="' . htmlspecialchars($estates[$i]->location) . '"></p>
                <p>Тип нерухомостi: <input type="text" name="estate_type" value="' . htmlspecialchars($estates[$i]->estate_type->value) . '"></p>
                <p>Тип договору: <input type="text" name="sale_type" value="' . htmlspecialchars($estates[$i]->sale_type->value) . '"></p>
                <p>Площа: <input type="text" name="area" value="' . htmlspecialchars($estates[$i]->area) . '"></p>
                <p>Опис: <input type="text" name="descriptiom" value="' . htmlspecialchars($estates[$i]->description) . '"></p>
                <p>Володар нерухомостi: <input type="text" name="owner_" value="' . htmlspecialchars(User::findByID($pdo, $estates[$i]->owner_id)->fullname) . '"></p>
                <p>Рiелтор: <input type="text" name="realtor" value="' . htmlspecialchars(User::findByID($pdo,$estates[$i]->realtor_id)->fullname) . '"></p>
                <p>Цiна: <input type="text" name="price" value="' . htmlspecialchars($estates[$i]->price) . '"></p>
                <input type="submit" value="Зберегти">
            </form>';
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