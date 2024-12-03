<?php

/*
if (!isset($_COOKIE['token'])) {
    print 'Немае доступу!';
    exit;
}
*/
if (!isset($_GET['user']) || !isset($_GET['mode'])) {
    print 'Немае користувача пiд таким номером або внутрiшня помилка!';
    exit;
}

$mode = $_GET['mode'];
$id = $_GET['user'];

$conroller = new UserController($pdo);
switch ($mode) {
    case 'create':
        $conroller->create_form($id);
        break;
    case 'read':
        $conroller->read($id);
        break;
    case 'edit':
        $conroller->edit_show($id);
        break;
}

?>