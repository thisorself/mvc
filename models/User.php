<?php

include 'core/Model.php';

enum Role: string
{
    case OWNER = "owner";
    case REALTOR = "realtor";
    case CLIENT = "client";
}

class User extends Model
{
    public function __construct($pdo)
    {
        parent::__construct($pdo, 'users');
    }

    // Отримання всіх користувачів з таблиці "users"
    public static function all($pdo, $table = 'users')
    {
        return parent::all($pdo, $table);
    }

    // Знайти користувача за його ID
    public static function findByID(PDO $pdo, $id)
    {
        return parent::find($pdo, $id, 'users');
    }

    // Знайти користувача за його username
    public static function findByUsername(PDO $pdo, $username)
    {
        $sql = "SELECT * FROM users WHERE username = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $username);

        if ($stmt->execute()) {
            $res = $stmt->fetch(PDO::FETCH_OBJ);
            if ($res) {
                $user = new User($pdo);
                $user->fields->id = $res->id;
                $user->fields->username = $res->username;
                $user->fields->password = $res->password;
                $user->fields->fullname = $res->fullname;
                $user->fields->phone = $res->phone;
                $user->fields->email = $res->email;
                $user->fields->role = Role::from($res->role);
                return $user;
            }
        } else
            return null;
    }

    // Знайти користувача за його fullname
    public static function findByFullname(PDO $pdo, $fullname)
    {
        $sql = "SELECT * FROM users WHERE fullname = :fullname";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':fullname', $fullname);

        if ($stmt->execute()) {
            $res = $stmt->fetch(PDO::FETCH_OBJ);
            if ($res) {
                $user = new User($pdo);
                $user->fields->id = $res->id;
                $user->fields->username = $res->username;
                $user->fields->password = $res->password;
                $user->fields->fullname = $res->fullname;
                $user->fields->phone = $res->phone;
                $user->fields->email = $res->email;
                $user->fields->role = Role::from($res->role);
                return $user;
            }
        }

        return null;
    }

    // Створення нового користувача
    public static function create($pdo, $fields = null, $table = 'users')
    {   
        if ($fields) {
            return parent::create($pdo, $fields, $table);
        }
    }
}