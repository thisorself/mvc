<?php
include 'config.php';
include 'User.php';
include 'RealEstate.php';

if (!isset($_COOKIE['token'])) {
    print 'Немае доступу!';
    exit;
}

if (!isset($_GET['real_estate'])) {
    print 'Немае нерухомостi пiд таким номером або внутрiшня помилка!';
    exit;
}

if ($_GET['do'] == 'delete') {
    $estate = RealEstate::find($pdo, $_GET['real_estate']);
    RealEstate::delete($pdo, $estate);
    print "Нерухомiсть успiшно видалена!";
}

if ($_GET['do'] == 'edit') {
    $errors = [];

    $location = isset($_POST['location']) ? $_POST['location'] : "";
    if ($location == "") {
        $errors []= "Мiсцерозташування пусте або некоректне!";
    }
    
    $estate_type = isset($_POST['estate_type']) ? $_POST['estate_type'] : "";
    if ($estate_type != "") {
        try {
            $estate_type = EstateType::from($estate_type);
        } catch (ValueError $e) {
            $errors []= "Тип нерухомостi некоректний!";
        }
    }
    else $errors []= "Тип нерухомостi пустий!";
    
    $sale_type = isset($_POST['sale_type']) ? $_POST['sale_type'] : "";
    if ($sale_type != "") {
        try {
            $sale_type = SaleType::from($sale_type);
        } catch (ValueError $e) {
            $errors []= "Тип договору некоректний!";
        }
    }
    else $errors []= "Тип договору пустий!";
    
    $area = isset($_POST['area']) ? $_POST['area'] : null;
    if ($area != null && is_numeric($area) && $area <= 0) {
        $errors []= "Площа некоректна!";
    }
    
    $description = isset($_POST['description']) ? $_POST['description'] : null;
    
    $owner = isset($_POST['owner']) ? $_POST['owner'] : null;
    if ($owner != null) {
        $user = User::findByFullname($pdo, $owner);
    
        if ($user) {
            if ($user->role == Role::OWNER) {
                $owner = $user->id;
            }
            else $errors []= "Користувач знайдений, але не з вiдповiдною роллю!";
        }
        else $errors []= "Користувач не знайдений!";
    }
    
    $realtor = isset($_POST['realtor']) ? $_POST['realtor'] : null;
    if ($realtor != null) {
        $user = User::findByFullname($pdo, $realtor);
    
        if ($user) {
            if ($user->role == Role::REALTOR) {
                $realtor = $user->id;
            }
            else $errors []= "Користувач знайдений, але не з вiдповiдною роллю!";
        }
        else $errors []= "Користувач не знайдений!";
    }
    
    $price = isset($_POST['price']) ? $_POST['price'] : null;
    if (is_numeric($price) && $price <= 0) {
        $errors []= "Цiна некоректна!";
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
    }
    else {
        foreach ($errors as $error) {
            print $error . "<br>";
        }
    }
}

print '<a href="estate.php?mode=read&real_estate=0">Повернутися</a>';

?>