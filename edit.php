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
    Controller::deleteEstate();
}
if ($_GET['do'] == 'edit') {
    Controller::editEstate();
}

print '<a href="estate.php?mode=read&real_estate=0">Повернутися</a>';

?>