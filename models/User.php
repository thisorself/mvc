<?php

include_once __DIR__ . '/../core/Model.php';

enum Role: string
{
    case OWNER = "owner";
    case REALTOR = "realtor";
    case CLIENT = "client";
}

class User extends Model
{
    public function __construct($pdo, $table = 'users', $data = null)
    {
        parent::__construct($pdo, $table, $data);
    }

    // Отримання всіх користувачів з таблиці "users"
    public static function all($pdo, $obj = 'User', $table = 'users')
    {
        return parent::all($pdo, $obj, $table);
    }

    // Знайти користувача за його ID
    public static function find($pdo, $id, $obj = 'User', $table = 'users')
    {
        return parent::find($pdo, $id, $obj, $table);
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

    public static function isExists($pdo) {
        $username = isset($_POST['username']) ? $_POST['username'] : "";
        $password = isset($_POST['password']) ? $_POST['password'] : "";
    
        $user = User::findByUsername($pdo, $username);
    
        if ($user && password_verify($password, $user->fields->password)) {
            setcookie('token', $user->fields->password, time() + 2000);
            setcookie('user_id', $user->fields->id, time() + 2000);
            return $user;
        }
        else 
            return null;
    }

    public static function hasMany($pdo, $where, $table = 'users') {
        return parent::hasMany($pdo, $where, $table);
    }

    public static function getByRole($role) {
        global $pdo;
        $all_users = User::all($pdo);
        
        $want_users = [];
        foreach ($all_users as $user) {
            if ($user->fields->role == $role->value) {
                $want_users []= $user;
            }
        }
        return $want_users;
    }
}