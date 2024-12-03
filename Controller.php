<?php
include_once 'core/connection.php';
include_once 'core/Model.php';
include_once 'models/User.php';
include_once 'models/RealEstate.php';

class Controller
{
    public static function showReadEstate($id)
    {
        global $pdo;
        $estates = RealEstate::all($pdo);

        $list = "";
        if ($id > 0)
            $list .= '<a href="estate.php?mode=read&real_estate=' . $id - 1 . '">Назад</a>   ';
        if ($id != count($estates) - 1)
            $list .= '<a href="estate.php?mode=read&real_estate=' . $id + 1 . '">Вперед</a><br>';
        else
            $list .= '<br>';

        $estate = $estates[$id];
        print
            '<p>Номер нерухомостi: ' . $estate->fields->id . '</p>
            <p>Мiсце знаходження:' . $estate->fields->location . '</p>
            <p>Тип нерухомостi:' . $estate->fields->estate_type . '</p>
            <p>Тип договору:' . $estate->fields->sale_type . '</p>
            <p>Площа:' . $estate->fields->area . '</p>
            <p>Опис:' . $estate->fields->description . '</p>
            <p>Володар нерухомостi:' . $estate->getUserFullname(Role::OWNER) . '</p>
            <p>Рiелтор:' . $estate->getUserFullname(Role::REALTOR) . '</p>
            <p>Цiна:' . $estate->fields->price . '</p>' . $list . '
            <a href="estate.php?mode=edit&real_estate=' . $id . '">Редагувати</a><br>
            <a href="edit.php?do=delete&real_estate=' . $estate->fields->id . '">Видалити</a>';
    }

    public static function showEditEstate($id)
    {
        global $pdo;
        $estates = RealEstate::all($pdo);
        $estate = $estates[$id];

        print
            '<form action="edit.php?do=edit&real_estate=' . $estate->fields->id . '" method="POST">
            <p>Номер нерухомостi: ' . $estate->fields->id . '</p>
            <p>Мiсце знаходження: <input type="text" name="location" value="' . $estate->fields->location . '"></p>
            <p>Тип нерухомостi: <input type="text" name="estate_type" value="' . $estate->fields->estate_type . '"></p>
            <p>Тип договору: <input type="text" name="sale_type" value="' . $estate->fields->sale_type . '"></p>
            <p>Площа: <input type="text" name="area" value="' . $estate->fields->area . '"></p>
            <p>Опис: <input type="text" name="description" value="' . $estate->fields->description . '"></p>
            <p>Володар нерухомостi: <input type="text" name="owner" value="' . $estate->getUserFullname(Role::OWNER) . '"></p>
            <p>Рiелтор: <input type="text" name="realtor" value="' . $estate->getUserFullname(Role::REALTOR) . '"></p>
            <p>Цiна: <input type="text" name="price" value="' . $estate->fields->price . '"></p>
            <input type="submit" value="Зберегти">     
            </form>';
    }

    public static function editEstate()
    {
        global $pdo;

        $errors = [];

        $location = isset($_POST['location']) ? $_POST['location'] : "";
        if ($location == "") {
            $errors[] = "Мiсцерозташування пусте або некоректне!";
        }

        $estate_type = isset($_POST['estate_type']) ? $_POST['estate_type'] : "";
        if ($estate_type != "") {
            try {
                $estate_type = EstateType::from($estate_type);
            } catch (ValueError $e) {
                $errors[] = "Тип нерухомостi некоректний!";
            }
        } else
            $errors[] = "Тип нерухомостi пустий!";

        $sale_type = isset($_POST['sale_type']) ? $_POST['sale_type'] : "";
        if ($sale_type != "") {
            try {
                $sale_type = SaleType::from($sale_type);
            } catch (ValueError $e) {
                $errors[] = "Тип договору некоректний!";
            }
        } else
            $errors[] = "Тип договору пустий!";

        $area = isset($_POST['area']) ? $_POST['area'] : null;
        if ($area != null && is_numeric($area) && $area <= 0) {
            $errors[] = "Площа некоректна!";
        }

        $description = isset($_POST['description']) ? $_POST['description'] : null;

        $owner = isset($_POST['owner']) ? $_POST['owner'] : null;
        if ($owner != null) {
            $user = User::findByFullname($pdo, $owner);

            if ($user) {
                if ($user->fields->role == Role::OWNER) {
                    $owner = $user->fields->id;
                } else
                    $errors[] = "Користувач знайдений, але не з вiдповiдною роллю!";
            } else
                $errors[] = "Користувач не знайдений!";
        }

        $realtor = isset($_POST['realtor']) ? $_POST['realtor'] : null;
        if ($realtor != null) {
            $user = User::findByFullname($pdo, $realtor);

            if ($user) {
                if ($user->fields->role == Role::REALTOR) {
                    $realtor = $user->fields->id;
                } else
                    $errors[] = "Користувач знайдений, але не з вiдповiдною роллю!";
            } else
                $errors[] = "Користувач не знайдений!";
        }

        $price = isset($_POST['price']) ? $_POST['price'] : null;
        if (is_numeric($price) && $price <= 0) {
            $errors[] = "Цiна некоректна!";
        }

        if (!$errors) {
            $estate = RealEstate::find($pdo, $_GET['real_estate']);
            $estate->fields->location = $location;
            $estate->fields->estate_type = $estate_type;
            $estate->fields->sale_type = $sale_type;
            $estate->fields->area = $area;
            $estate->fields->description = $description;
            $estate->fields->owner_id = empty($owner) ? null : $owner;
            $estate->fields->realtor_id = empty($realtor) ? null : $realtor;
            $estate->fields->price = $price;
            $estate->update();
            print "Нерухомiсть успiшно оновлена!";
        } else {
            foreach ($errors as $error) {
                print $error . "<br>";
            }
        }
    }

    public static function deleteEstate()
    {
        global $pdo;
        RealEstate::find($pdo, $_GET['real_estate'])->delete();
        print "Нерухомiсть успiшно видалена!";
    }

    public static function showAllEstate()
    {
        global $pdo;
        $estates = RealEstate::all($pdo);

        if (count($estates) == 0) {
            "Немае записiв у таблицi!";
            exit;
        }

        print "<table border='2'><tr>";
        foreach ($estates[0]->fields as $key => $value) {
            print "<th>$key</th>";
        }
        print "</tr>";

        foreach ($estates as $estate) {
            print "<tr>";
            print "<td>" . $estate->fields->id . "</td>";
            print "<td>" . $estate->fields->location . "</td>";
            print "<td>" . $estate->fields->estate_type . "</td>";
            print "<td>" . $estate->fields->sale_type . "</td>";
            print "<td>" . $estate->fields->area . "</td>";
            print "<td>" . $estate->fields->description . "</td>";
            print "<td>" . $estate->getUserFullname(Role::OWNER) . "</td>";
            print "<td>" . $estate->getUserFullname(Role::REALTOR) . "</td>";
            print "<td>" . $estate->fields->price . "</td>";
            print "</tr>";
        }
    }

    public static function CheckLogIn() {
        global $pdo;
        if (!isset($_COOKIE['token'])) {
            print '<h4>Незареестрований гiсть!</h4>
                   <form action="register.php" method="POST">
                   <p>Введiть нiкнейм:</p><input type="text" name="username">
                   <p>Введiть пароль:</p><input type="text" name="password"><br>
                   <input type="submit" value="Зарееструватися"></button>';
        }
        else {
            $user = User::find($pdo, $_COOKIE['user_id']);
            print $user->fields->fullname . " вітаємо!<br>";
            setcookie('token', $user->fields->password, time() + 2000);
            setcookie('user_id', $user->fields->id, time() + 2000);
            print "<a href='estate.php?mode=read&real_estate=0'>Перейти до перегляду нерухомостi.</a>";
        }
    }

    public static function EnumError(&$value) {
        if (is_object($value)) {
            if (method_exists($value, '__toString')) {
                $value = (string)$value;
            } elseif (property_exists($value, 'value')) {
                $value = (string)$value->value;
            } else {
                die('enum or else error');
            }
        }
    }
}

?>