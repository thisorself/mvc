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
    public function __construct($pdo)
    {
        parent::__construct($pdo, 'real_estates');
    }

    // Отримання всіх нерухомістей з таблиці "real_estates"
    public static function all($pdo, $table = 'real_estates')
    {
        return parent::all($pdo, $table);
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
}