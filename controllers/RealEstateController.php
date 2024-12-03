<?php

include_once '../core/Controller.php';

class RealEstateController extends Controller {
    public function __construct($pdo) {
        parent::__construct($pdo);
    }

    public function index() {
        $estates = RealEstate::all($this->pdo);
        if (count($estates) == 0) {
            "Немае нерухомостей!";
            exit;
        }

        print '<h1>Список нерухомостей</h1>';
        print "<table border='2'><tr>";
        foreach ($estates[0]->fields as $key => $value) {
            print "<th>$key</th>";
        }
        print "</tr>";

        foreach ($estates as $estate) {
            print "<tr>";
            foreach ($estate->fields as $key => $value) {
                print "<td>";

                switch ($key) {
                    case 'owner_id':
                        $value = $estate->getUserFullname(Role::OWNER);
                        break;
                    case 'realtor_id':
                        $value = $estate->getUserFullname(Role::REALTOR);
                        break;
                }

                print $value;
                print "</td>";
            }
            print "</tr>";
        }
        print '</table>';
    }

    public function show($id) {
        $estate = RealEstate::find($this->pdo, $id);
        if (!$estate) {
            print "Немае такоi нерухомостi!";
            exit;
        }

        print '<h1>Нерухомiсть</h1>';
        print "<table border='2'><tr>";
        foreach ($estate->fields as $key => $value) {
            print "<th>$key</th>";
        }
        print "</tr>";

        print "<tr>";
        foreach ($estate->fields as $key => $value) {
            print "<td>";

            switch ($key) {
                case 'owner_id':
                    $value = $estate->getUserFullname(Role::OWNER);
                    break;
                case 'realtor_id':
                    $value = $estate->getUserFullname(Role::REALTOR);
                    break;
            }

            print $value;
            print "</td>";
        }
        print "</tr>";
        print '</table>';
    }

    public function read($id) {
        $estate = RealEstate::find($this->pdo, $id);
        $count = Model::count($this->pdo, 'real_estates');
    
        $list = "";
        if ($id > 1)
            $list .= '<a href="estate.php?mode=read&real_estate=' . $id - 1 . '">Назад</a>   ';
        if ($id != $count)
            $list .= '<a href="estate.php?mode=read&real_estate=' . $id + 1 . '">Вперед</a><br>';
        else
            $list .= '<br>';

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
            <a href="estate.php?mode=create&real_estate=' . $count + 1 . '">Додати</a><br>
            <a href="estate.php?mode=edit&real_estate=' . $id . '">Редагувати</a><br>
            <a href="edit.php?do=delete&real_estate=' . $estate->fields->id . '">Видалити</a><br>
            <a href="../index.php">Повернутися</a>';
    }

    public function edit_show($id) {
        $estate = RealEstate::find($this->pdo, $id);

        print
            '<form action="edit.php?do=edit&real_estate=' . $estate->fields->id . '" method="POST">
            <p>Номер нерухомостi: ' . $estate->fields->id . '</p>
            <p>Мiсце знаходження: <input type="text" name="location" value="' . $estate->fields->location . '"></p>
            <p>Тип нерухомостi: <input type="text" name="estate_type" value="' . $estate->fields->estate_type . '"></p>
            <p>Тип договору: <input type="text" name="sale_type" value="' . $estate->fields->sale_type . '"></p>
            <p>Площа: <input type="text" name="area" value="' . $estate->fields->area . '"></p>
            <p>Опис: <input type="text" name="description" value="' . $estate->fields->description . '"></p>
            <p>Володар нерухомостi: <input type="text" name="owner" value="' . $estate->getUserFullname(Role::OWNER, true) . '"></p>
            <p>Рiелтор: <input type="text" name="realtor" value="' . $estate->getUserFullname(Role::REALTOR, true) . '"></p>
            <p>Цiна: <input type="text" name="price" value="' . $estate->fields->price . '"></p>
            <a href="estate.php?mode=read&real_estate=' . $id . '">Повернутися до перегляду</a><br>
            <input type="submit" value="Зберегти">     
            </form>';
    }

    public function correct($do) {
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
            $user = User::findByFullname($this->pdo, $owner);

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
            $user = User::findByFullname($this->pdo, $realtor);

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
            $fields = [
                'id' => $_GET['real_estate'], 
                'location' => $location, 
                'estate_type' => $estate_type, 
                'sale_type' => $sale_type, 
                'area' => $area, 
                'description' => $description, 
                'owner_id' => $owner, 
                'realtor_id' => $realtor, 
                'price' => $price
            ];
            switch ($do) {
                case 'edit':
                    $this->edit($fields);
                    break;
                case 'create':
                    $this->create($fields);
                    break;
            }
        } else {
            foreach ($errors as $error) {
                print $error . "<br>";
            }
        }
    }

    private function edit($fields) {
        $estate = RealEstate::find($this->pdo, $_GET['real_estate']);

        foreach ($fields as $key => $value) {
            if ($key == 'id') continue;

            switch ($key) {
                case 'owner_id':
                    $value = empty($value) ? null : $value;
                    break;
                case 'realtor_id':
                    $value = empty($value) ? null : $value;
                    break;
            }

            $estate->fields->$key = $value;
        }

        $estate->update();
        print "Нерухомiсть успiшно оновлена!<br>";
    }

    private function create($fields) {
        $estate = RealEstate::create($this->pdo, $fields);
        $estate->insert();
        print "Нерухомiсть успiшно створена!";
    }

    public function delete($id) {
        RealEstate::find($this->pdo, $id)->delete();
        print "Нерухомiсть успiшно видалена!";
    }

    public function create_form($id) {
        print
            '<h1>Створення нерухомостi</h1><br>
            <form action="edit.php?do=create&real_estate=' . $id . '" method="POST">
            <p>Номер нерухомостi:' . $id . '</p>
            <p>Мiсце знаходження: <input type="text" name="location"></p>
            <p>Тип нерухомостi: <input type="text" name="estate_type"></p>
            <p>Тип договору: <input type="text" name="sale_type"></p>
            <p>Площа: <input type="text" name="area"></p>
            <p>Опис: <input type="text" name="description"></p>
            <p>Володар нерухомостi: <input type="text" name="owner"></p>
            <p>Рiелтор: <input type="text" name="realtor"></p>
            <p>Цiна: <input type="text" name="price"></p>
            <input type="submit" value="Створити">     
            </form>';
    }

    public function store($estate) {
        $estate->insert();
        print '<h1>Нерухомiсть успiшно збережено!</h1>';
        print '<a href="estate.php?mode=read&real_estate=1">Повернутися</a>';
    }
}