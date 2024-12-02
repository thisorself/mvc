<?php
include 'config.php';
include 'RealEstate.php';
include 'User.php';

if (!isset($_COOKIE['token'])) {
    print 'Немае доступу!';
    exit;
}

if (!isset($_GET['real_estate']) || !isset($_GET['mode'])) {
    print 'Немае нерухомостi пiд таким номером або внутрiшня помилка!';
    exit;
}

$estates = RealEstate::all($pdo);
$estate = $estates[$_GET['real_estate']];
$mode = $_GET['mode'];

if ($mode == 'read') {
    print 
        '<p>Номер нерухомостi: ' . $estate->id . '</p>
        <p>Мiсце знаходження:' . $estate->location . '</p>
        <p>Тип нерухомостi:' . $estate->estate_type->value . '</p>
        <p>Тип договору:' . $estate->sale_type->value . '</p>
        <p>Площа:' . $estate->area . '</p>
        <p>Опис:' . $estate->description . '</p>
        <p>Володар нерухомостi:' . User::findByID($pdo, $estate->owner_id)->fullname . '</p>
        <p>Рiелтор:' . User::findByID($pdo, $estate->realtor_id)->fullname . '</p>
        <p>Цiна:' . $estate->price . '</p>
        <a href="estate.php?mode=edit&real_estate=' . $_GET['real_estate'] . '">Редагувати</a><br>
        <a href="estate.php?mode=read&real_estate=0">Видалити</a>';
}

if ($mode == 'edit') {
    print
        '<form action="save.php" method="POST">
        <p>Номер нерухомостi: ' . $estate->id . '</p>
        <p>Мiсце знаходження: <input type="text" name="location" value="' . htmlspecialchars($estate->location) . '"></p>
        <p>Тип нерухомостi: <input type="text" name="estate_type" value="' . htmlspecialchars($estate->estate_type->value) . '"></p>
        <p>Тип договору: <input type="text" name="sale_type" value="' . htmlspecialchars($estate->sale_type->value) . '"></p>
        <p>Площа: <input type="text" name="area" value="' . htmlspecialchars($estate->area) . '"></p>
        <p>Опис: <input type="text" name="descriptiom" value="' . htmlspecialchars($estate->description) . '"></p>
        <p>Володар нерухомостi: <input type="text" name="owner_" value="' . htmlspecialchars(User::findByID($pdo, $estate->owner_id)->fullname) . '"></p>
        <p>Рiелтор: <input type="text" name="realtor" value="' . htmlspecialchars(User::findByID($pdo, $estate->realtor_id)->fullname) . '"></p>
        <p>Цiна: <input type="text" name="price" value="' . htmlspecialchars($estate->price) . '"></p>
        <input type="submit" value="Зберегти">    
        <input type="submit" value="Видалити">    
        </form>';
}

?>