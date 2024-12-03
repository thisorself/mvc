<?php
include_once 'core/connection.php';
include_once 'Controller.php';
include_once 'models/User.php';

if (!isset($_COOKIE['token'])) {
    print 'Немае доступу!';
    exit;
}

if (!isset($_GET['real_estate']) || !isset($_GET['mode'])) {
    print 'Немае нерухомостi пiд таким номером або внутрiшня помилка!';
    exit;
}

$mode = $_GET['mode'];

if ($mode == 'read') {
    Controller::showReadEstate($_GET['real_estate']);
}
if ($mode == 'edit') {
    Controller::showEditEstate($_GET['real_estate']);
}

?>