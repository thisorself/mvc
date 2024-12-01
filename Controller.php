<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $location = $_POST['location'];
    $estate_type = $_POST['estate_type'];
    $sale_type = $_POST['sale_type'];
    $price = $_POST['price'];
    $estate_id = $_POST['estate_id'];

    // Пример функции для сохранения данных (необходимо адаптировать под вашу логику)
    // Сохранение данных в базу данных, например:
    $query = $pdo->prepare("UPDATE real_estates SET location = ?, estate_type = ?, sale_type = ?, price = ? WHERE id = ?");
    $query->execute([$location, $estate_type, $sale_type, $price, $estate_id]);

    echo "Данные сохранены!";
}
?>