<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="utf-8">
    <title>MVC</title>
</head>

<body>

    <?php
    include_once 'bootstrap.php';

    $router = new Router();
    //
    $router->addRoute('GET', '/mvc/login', [UserController::class, $pdo, 'login']);

    $router->addRoute('GET', '/mvc/estates/all', [RealEstateController::class, $pdo, 'index']);
    $router->addRoute('GET', '/mvc/estates/{id}', [RealEstateController::class, $pdo, 'show']);

    $router->addRoute('GET', '/mvc/estates/read/{id}', [RealEstateController::class, $pdo, 'read']);
    $router->addRoute('GET', '/mvc/estates/edit/{id}', [RealEstateController::class, $pdo, 'edit']);

    $router->addRoute('GET', '/mvc/users/all', [UserController::class, $pdo, 'index']);
    $router->addRoute('GET', '/mvc/users/{id}', [UserController::class, $pdo, 'show']);

    $router->addRoute('GET', '/mvc/users/read/{id}', [RealEstateController::class, $pdo, 'read']);
    $router->addRoute('GET', '/mvc/users/edit/{id}', [RealEstateController::class, $pdo, 'edit']);

    $router->handleRequest();
    ?>

</body>

</html>