<?php

include_once 'core/Model.php';

enum EstateType: string {
    case APARTMENT = "Apartment";
    case HOUSE = "House";
    case COMMERCIAL = "Commercial";
    case LAND = "Land";
    case GARAGE = "Garage";
}

enum SaleType: string {
    case SALE = "Sale";
    case RENT = "Rent";
    case SUBLET = "Sublet";
}

class RealEstate extends Model
{
    public function __construct($pdo, $table = 'real_estates', $data = null)
    {
        parent::__construct($pdo, $table, $data);
    }

    // Отримання всіх нерухомістей з таблиці "real_estates"
    public static function all($pdo, $obj = 'RealEstate', $table = 'real_estates')
    {
        return parent::all($pdo, $obj,$table);
    }

    public static function find($pdo, $id, $table = 'real_estates') {
        return parent::find($pdo, $id, $table);
    }

    // Створення новоi нерухомостi
    public static function create($pdo, $fields = null, $table = 'real_estates')
    {   
        if ($fields) {
            return parent::create($pdo, $fields, $table);
        }
    }

    public static function hasMany($pdo, $where, $table = 'real_estates') {
        return parent::hasMany($pdo, $where, $table);
    }

    public function getUserFullname($role) {
        switch ($role) {
            case Role::OWNER:
                $user = $this->belongsTo('User', $this->fields->owner_id);
                if ($user) return $user->fields->fullname;
                else return "Вiдсутнiй/я";
            case Role::REALTOR:
                $user = $this->belongsTo('User', $this->fields->realtor_id);
                if ($user) return $user->fields->fullname;
                else return "Вiдсутнiй/я";
        }
    }
}