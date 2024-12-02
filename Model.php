<?php

class Model {
    public $pdo;
    public $table = "";
    public $fields;

    public function __construct($pdo, $table, $data = null) {
        $this->pdo = $pdo;
        if (!$this->table)
            $this->table = $table;
        $this->fields = (object) [];

        $sql = "SHOW COLUMNS FROM " . $this->table;
        $statement = $pdo->query($sql);

        $columns = $statement->fetchAll(PDO::FETCH_OBJ);

        foreach ($columns as $column) {
            $fd = $column->Field;
            $this->fields->$fd = null;
        }
        if ($data) {
            foreach ($data as $key => $value) {
                $this->fields->$key = $value;
            }
        }
    }

    public static function all($pdo, $table) {
        $sql = "SELECT * FROM $table";
        $statement = $pdo->query($sql);
        $records = $statement->fetchAll(PDO::FETCH_OBJ);

        $models = [];
        foreach($records as $record) {
            $model = new Model($pdo, $table, $record);
            $models []= $model;
        }
        return $models;
    }

    public static function find($pdo, $table, $id) {
        $sql = "SELECT * FROM $table WHERE id = :id";
        $statement = $pdo->prepare($sql);
        $statement->bindParam(":id", $id);
        $statement->execute();

        if ($statement->rowCount() > 0) {
            $record = $statement->fetch(PDO::FETCH_OBJ);
            return new Model($pdo, $table, $record);
        }
        else return null;
    }

    public static function count($pdo, $table) {
        $sql = "SELECT COUNT(*) FROM $table";
        $statement = $pdo->query($sql);
        return $statement->fetch(PDO::FETCH_OBJ)->total;
    }
}