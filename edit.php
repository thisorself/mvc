<?php
include_once 'core/connection.php';
include_once 'models/User.php';
include_once 'models/RealEstate.php';
include_once 'Controller.php';

if (!isset($_COOKIE['token'])) {
    print 'Немае доступу!';
    exit;
}

if (!isset($_GET['real_estate'])) {
    print 'Немае нерухомостi пiд таким номером або внутрiшня помилка!';
    exit;
}

if ($_GET['do'] == 'delete') {
    Controller::deleteEstate();
}
if ($_GET['do'] == 'edit') {
    Controller::editEstate();
}

print '<a href="estate.php?mode=read&real_estate=0">Повернутися</a>';

?>