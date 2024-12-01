<?php

enum EstateType {
    case APARTMENT;
    case HOUSE;
    case COMMERCIAL;
    case LAND;
    case GARAGE;
}

enum SaleType {
    case SALE;
    case RENT;
    case SUBLET;
}

class RealEstate
{
    private $pdo;
    public $id;
    public $location;
    public EstateType $estate_type;
    public SaleType $sale_type;
    public $area;
    public $description;
    public $owner_id;
    public $realtor_id;
    public $price;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Отримання всіх нерухомістей з таблиці "real_estates"
    public static function all(PDO $pdo)
    {
        $sql = "SELECT * FROM real_estates";
        $stmt = $pdo->query($sql);

        $estates = [];

        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $estate = new RealEstate($pdo);
            $estate->id = $row->id;
            $estate->location = $row->location;
            $estate->estate_type = $row->estate_type;
            $estate->sale_type = $row->sale_type;
            $estate->area = $row->area;
            $estate->description = $row->description;
            $estate->owner_id = $row->owner_id;
            $estate->realtor_id = $row->realtor_id;
            $estate->price = $row->price;
            $estates[] = $estate;
        }

        return $estates;
    }

    // Знайти нерухомість за його ID
    public static function find(PDO $pdo, $id)
    {
        $sql = "SELECT * FROM real_estates WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            $res = $stmt->fetch(PDO::FETCH_OBJ);
            if ($res) {
                $estate = new RealEstate($pdo);
                $estate->id = $res->id;
                $estate->location = $res->location;
                $estate->estate_type = $res->estate_type;
                $estate->sale_type = $res->sale_type;
                $estate->area = $res->area;
                $estate->description = $res->description;
                $estate->owner_id = $res->owner_id;
                $estate->realtor_id = $res->realtor_id;
                $estate->price = $res->price;
                return $estate;
            }
        }

        return null;
    }

    // Додавання новоi нерухомостi
    public static function create($pdo, $location, $estate_type, $sale_type, $area, $description, $owner_id, $realtor_id, $price)
    {
        $sql = "INSERT INTO users (location, estate_type, sale_type, area, description, owner_id, realtor_id, price) 
                VALUES (:location, :estate_type, :sale_type, :area, :description, :owner_id, :realtor_id, :price)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':location', $location);
        $stmt->bindParam(':estate_type', $estate_type);
        $stmt->bindParam(':sale_type', $sale_type);
        $stmt->bindParam(':area', $area);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':owner_id', $owner_id);
        $stmt->bindParam(':realtor_id', $realtor_id);
        $stmt->bindParam(':price', $price);

        if ($stmt->execute()) {
            $estate = new RealEstate($pdo);
            $estate->id = $pdo->lastInsertId();;
            $estate->location = $location;
            $estate->estate_type = $estate_type;
            $estate->sale_type = $sale_type;
            $estate->area = $area;
            $estate->description = $description;
            $estate->owner_id = $owner_id;
            $estate->realtor_id = $realtor_id;
            $estate->price = $price;
            return $estate;
        } else {
            return null;
        }
    }

    // Оновлення існуючоi нерухомостi
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

    // Видалення нерухомостi
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