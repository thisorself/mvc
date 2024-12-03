<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="utf-8">
    <title>MVC</title>
</head>

<body>

    <?php
    include_once 'core/connection.php';
    include_once 'models/User.php';
    include_once 'Controller.php';

    Controller::CheckLogIn();
    /*
    $realtors = User::getByRole(Role::OWNER);
    foreach ($realtors as $realtor) {
        $where = ['id' => $realtor->fields->id];
        $estates = RealEstate::hasMany($pdo, $where);
        foreach ($estates as $estate) {
            print $estate->fields->location;
        }
    }
        */


    ?>

</body>

</html>