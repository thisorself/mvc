<?php
include 'connection.php';
include 'Model.php';

class Controller
{
    public static function showReadEstate($id)
    {
        global $pdo;
        $estates = Model::all($pdo, 'real_estates');

        $list = "";
        if ($id > 0)
            $list .= '<a href="estate.php?mode=read&real_estate=' . $id - 1 . '">Назад</a>   ';
        if ($id != count($estates))
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
            <p>Володар нерухомостi:' . User::findByID($pdo, $estate->fields->owner_id)->fullname . '</p>
            <p>Рiелтор:' . User::findByID($pdo, $estate->fields->realtor_id)->fullname . '</p>
            <p>Цiна:' . $estate->fields->price . '</p>' . $list . '
            <a href="estate.php?mode=edit&real_estate=' . $id . '">Редагувати</a><br>
            <a href="edit.php?do=delete&real_estate=' . $estate->fields->id . '">Видалити</a>';
    }

    public static function showEditEstate($id)
    {
        global $pdo;
        $estates = Model::all($pdo, 'real_estates');
        $estate = $estates[$id];

        print
            '<form action="edit.php?do=edit&real_estate=' . $estate->fields->id . '" method="POST">
            <p>Номер нерухомостi: ' . $estate->fields->id . '</p>
            <p>Мiсце знаходження: <input type="text" name="location" value="' . $estate->fields->location . '"></p>
            <p>Тип нерухомостi: <input type="text" name="estate_type" value="' . $estate->fields->estate_type . '"></p>
            <p>Тип договору: <input type="text" name="sale_type" value="' . $estate->fields->sale_type . '"></p>
            <p>Площа: <input type="text" name="area" value="' . $estate->fields->area . '"></p>
            <p>Опис: <input type="text" name="description" value="' . $estate->fields->description . '"></p>
            <p>Володар нерухомостi: <input type="text" name="owner" value="' . User::findByID($pdo, $estate->fields->owner_id)->fullname . '"></p>
            <p>Рiелтор: <input type="text" name="realtor" value="' . User::findByID($pdo, $estate->fields->realtor_id)->fullname . '"></p>
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
                if ($user->role == Role::OWNER) {
                    $owner = $user->id;
                } else
                    $errors[] = "Користувач знайдений, але не з вiдповiдною роллю!";
            } else
                $errors[] = "Користувач не знайдений!";
        }

        $realtor = isset($_POST['realtor']) ? $_POST['realtor'] : null;
        if ($realtor != null) {
            $user = User::findByFullname($pdo, $realtor);

            if ($user) {
                if ($user->role == Role::REALTOR) {
                    $realtor = $user->id;
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
            $estate->location = $location;
            $estate->estate_type = $estate_type;
            $estate->sale_type = $sale_type;
            $estate->area = $area;
            $estate->description = $description;
            $estate->owner_id = empty($owner) ? null : $owner;
            $estate->realtor_id = empty($realtor) ? null : $realtor;
            $estate->price = $price;
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
        $estate = RealEstate::find($pdo, $_GET['real_estate']);
        RealEstate::delete($pdo, $estate);
        print "Нерухомiсть успiшно видалена!";
    }

    public static function showAllEstate()
    {
        include 'User.php';
        global $pdo;
        $estates = Model::all($pdo, 'real_estates');

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
            print "<td>" . $estate->fields->lastInsertId . "</td>";
            print "<td>" . $estate->fields->location . "</td>";
            print "<td>" . $estate->fields->estate_type . "</td>";
            print "<td>" . $estate->fields->sale_type . "</td>";
            print "<td>" . $estate->fields->area . "</td>";
            print "<td>" . $estate->fields->description . "</td>";
            print "<td>" . User::findByID($pdo, $estate->fields->owner_id)->fullname . "</td>";
            print "<td>" . User::findByID($pdo, $estate->fields->realtor_id)->fullname . "</td>";
            print "<td>" . $estate->fields->price . "</td>";
            print "</tr>";
        }
    }
}

?>