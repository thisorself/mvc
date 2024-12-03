<?php

/*
if (!isset($_COOKIE['token'])) {
    print 'Немае доступу!';
    exit;
}
    */

if (!isset($_GET['real_estate']) && !isset($_GET['user']) || (isset($_GET['real_estate']) && isset($_GET['user']))) {
    print 'Немае нерухомостi та користувача пiд таким номером або внутрiшня помилка!';
    exit;
}

$do = $_GET['do'];

if (isset($_GET['real_estate'])){
    $id = $_GET['real_estate'];
    $controller = new RealEstateController($pdo);
    $back = '<a href="estate.php?mode=read&real_estate=1">Повернутися</a>';
}
if (isset($_GET['user'])) {
    $id = $_GET['user'];
    $controller = new UserController($pdo);
    $back = '<a href="user.php?mode=read&user=1">Повернутися</a>';
}

switch ($do) {
    case 'create':
        $controller->correct($do);
        break;
    case 'redact':
        $controller->edit_show($id);
        break;
    case 'edit':
        $controller->correct($do);
        break;
    case 'delete':
        $controller->delete($id);
        break;
}

print $back;

?>