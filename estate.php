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
$i = $_GET['real_estate'];
$estate = $estates[$i];
$mode = $_GET['mode'];

if ($mode == 'read') {
    $list = "";
    if ($i != 0)
        $list .= '<a href="estate.php?mode=read&real_estate=' . $i - 1 . '">Назад</a><br>';
    if ($i + 1 != count($estates))
        $list .= '<a href="estate.php?mode=read&real_estate=' . $i + 1 . '">Вперед</a><br>';


    print 
        '<p>Номер нерухомостi: ' . $estate->id . '</p>
        <p>Мiсце знаходження:' . $estate->location . '</p>
        <p>Тип нерухомостi:' . $estate->estate_type->value . '</p>
        <p>Тип договору:' . $estate->sale_type->value . '</p>
        <p>Площа:' . $estate->area . '</p>
        <p>Опис:' . $estate->description . '</p>
        <p>Володар нерухомостi:' . User::findByID($pdo, $estate->owner_id)->fullname . '</p>
        <p>Рiелтор:' . User::findByID($pdo, $estate->realtor_id)->fullname . '</p>
        <p>Цiна:' . $estate->price . '</p>' . $list . '
        <a href="estate.php?mode=edit&real_estate=' . $i . '">Редагувати</a><br>
        <a href="edit.php?do=delete&real_estate=' . $estate->id . '">Видалити</a>';
}

if ($mode == 'edit') {
    print
        '<form action="edit.php?do=edit&real_estate=' . $estate->id . '" method="POST">
        <p>Номер нерухомостi: ' . $estate->id . '</p>
        <p>Мiсце знаходження: <input type="text" name="location" value="' . $estate->location . '"></p>
        <p>Тип нерухомостi: <input type="text" name="estate_type" value="' . $estate->estate_type->value . '"></p>
        <p>Тип договору: <input type="text" name="sale_type" value="' . $estate->sale_type->value . '"></p>
        <p>Площа: <input type="text" name="area" value="' . $estate->area . '"></p>
        <p>Опис: <input type="text" name="description" value="' . $estate->description . '"></p>
        <p>Володар нерухомостi: <input type="text" name="owner" value="' . User::findByID($pdo, $estate->owner_id)->fullname . '"></p>
        <p>Рiелтор: <input type="text" name="realtor" value="' . User::findByID($pdo, $estate->realtor_id)->fullname . '"></p>
        <p>Цiна: <input type="text" name="price" value="' . $estate->price . '"></p>
        <input type="submit" value="Зберегти">     
        </form>';
}

?>