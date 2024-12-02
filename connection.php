<?php
$database_host = 'MySQL-8.2';
$database_username = 'root';
$database_password = '';
$database_name = 'realtor_catalog';

try {
    $pdo = new PDO("mysql:host=$database_host;dbname=$database_name", $database_username, $database_password);
} catch (PDOException $e){
    die('Error : (' . $e->getMessage() . ')');
}