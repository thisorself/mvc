<?php

enum Role: string {
    case OWNER = "owner";
    case REALTOR = "realtor";
    case CLIENT = "client";
}

class User
{
    private $pdo;
    public $id;
    public $username;
    public $password;
    public $fullname;
    public $phone;
    public $email;
    public Role $role;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Отримання всіх користувачів з таблиці "users"
    public static function all(PDO $pdo)
    {
        $sql = "SELECT * FROM users";
        $stmt = $pdo->query($sql);

        $users = [];

        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $user = new User($pdo);
            $user->id = $row->id;
            $user->username = $row->username;
            $user->password = $row->password;
            $user->fullname = $row->fullname;
            $user->phone = $row->phone;
            $user->email = $row->email;
            $user->role = Role::from($row->role);
            $users[] = $user;
        }

        return $users;
    }

    // Знайти користувача за його ID
    public static function findByID(PDO $pdo, $id)
    {
        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            $res = $stmt->fetch(PDO::FETCH_OBJ);
            if ($res) {
                $user = new User($pdo);
                $user->id = $res->id;
                $user->username = $res->username;
                $user->password = $res->password;
                $user->fullname = $res->fullname;
                $user->phone = $res->phone;
                $user->email = $res->email;
                $user->role = Role::from($res->role);
                return $user;
            }
        }

        return null;
    }

    // Знайти користувача за його username та password
    public static function findByUsername(PDO $pdo, $username)
    {
        $sql = "SELECT * FROM users WHERE username = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $username);

        if ($stmt->execute()) {
            $res = $stmt->fetch(PDO::FETCH_OBJ);
            if ($res) {
                $user = new User($pdo);
                $user->id = $res->id;
                $user->username = $res->username;
                $user->password = $res->password;
                $user->fullname = $res->fullname;
                $user->phone = $res->phone;
                $user->email = $res->email;
                $user->role = Role::from($res->role);
                return $user;
            }
        }

        return null;
    }

    // Додавання нового користувача
    public static function create($pdo, $username, $password, $fullname, $phone, $email, $role)
    {
        $sql = "INSERT INTO users (username, password, fullname, phone, email, role) 
                VALUES (:username, :password, :fullname, :phone, :email, :role)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', password_hash($password, PASSWORD_DEFAULT));
        $stmt->bindParam(':fullname', $fullname);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':role', $role);

        if ($stmt->execute()) {
            $user = new User($pdo);
            $user->id = $pdo->lastInsertId();;
            $user->username = $username;
            $user->password = $password;
            $user->fullname = $fullname;
            $user->phone = $phone;
            $user->email = $email;
            $user->role = Role::from($role);
            return $user;
        } else {
            return null;
        }
    }

    // Оновлення існуючого користувача
    public function update()
    {
        $sql = "UPDATE users SET username = :username, password = :password, fullname = :fullname,
                                 phone = :phone, email = :email, role = :role WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':fullname', $this->fullname);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':role', $this->role);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Видалення користувача
    public static function delete(PDO $pdo, User &$user)
    {
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $user->id);

        if ($stmt->execute()) {
            unset($user);
            return true;
        } else {
            return false;
        }
    }
}