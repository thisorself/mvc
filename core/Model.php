<?php

class Model {
    protected $pdo;
    protected $table;
    public $fields;

    public function __construct($pdo, $table, $data = null) {
        $this->pdo = $pdo;
        if (!$this->table)
            $this->table = $table;
        $this->fields = (object) [];

        $columns = static::getTableColumns($pdo, $table);

        if ($columns) {
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
    }

    protected static function getTableColumns($pdo, $table) {
        $sql = "SHOW COLUMNS FROM $table";
        $statement = $pdo->query($sql);

        if ($statement->execute()) {
            return $statement->fetchAll(PDO::FETCH_OBJ);
        }
        else return null;
    }

    protected static function all($pdo, $table = null) {
        if ($table) {
            $sql = "SELECT * FROM :table";
            $statement = $pdo->prepare($sql);
            $statement->bindParam(":table", $table);
    
            $models = [];
            if ($statement->execute()) {
                $records = $statement->fetchAll(PDO::FETCH_OBJ);
                foreach($records as $record) {
                    $model = new Model($pdo, $table, $record);
                    $models []= $model;
                }
            }
            return $models;
        }
        else return null;
    }

    public static function find($pdo, $id, $table = null) {
        if ($table) {
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
        else return null;
    }

    public static function count($pdo, $table) {
        $sql = "SELECT COUNT(*) FROM $table";
        $statement = $pdo->query($sql);
        return $statement->fetch(PDO::FETCH_OBJ)->total;
    }

    public static function create($pdo, $fields = null, $table = null) {
        if ($table && $fields) {
            $table_columns = static::getTableColumns($pdo, $table);
            if ($table_columns && count($table_columns) == count($fields)) {
                $model = new Model($pdo, $table);

                for ($i = 0; $i < count($table_columns); $i++) {
                    $fd = $table_columns[$i]->Field;
                    $model->fields->$fd = $fields[$i];
                }
                return $model;
            }
            else return null;
        }
        else return null;
    }

    public function insert() {
        $table_columns = static::getTableColumns($this->pdo, $this->table);
        if ($table_columns) {
            array_shift($table_columns);
            $sql = 'INSERT INTO ' . $this->table . ' (';

            $columns = [];
            $parameters = [];
            foreach ($table_columns as $table_column) {
                $columns []= $table_column->Field;
                $parameters []= ':' . $table_column->Field;
            }
            $sql .= implode(', ', $columns) . ') VALUES (' . implode(', ', $parameters) . ')';

            $statement = $this->pdo->prepare($sql);

            foreach ($columns as $column) {
                $statement->bindParam(':' . $column, $this->fields->$column);
            }

            if ($statement->execute()) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function update() {
        $table_columns = static::getTableColumns($this->pdo, $this->table);
        if ($table_columns) {
            $sql = 'UPDATE ' . $this->table . ' SET ';
            
            $pairs = [];
            foreach ($table_columns as $table_column) {
                $pairs []= $table_column->Field . ' = :' . $table_column->Field; 
            }

            $id = $pairs[0];
            $parameters = $pairs;
            array_shift($parameters);
            $parameters = implode(', ', $parameters);
            $sql .= $parameters . ' WHERE ' . $id;

            $statement = $this->pdo->prepare($sql);

            foreach ($pairs as $pair) {
                $p = explode(' = ', $pair)[0];
                $statement->bindParam(':' . $p, $this->fields->$p);
            }

            if ($statement->execute()) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function delete()
    {
        $sql = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $this->fields->id);

        if ($stmt->execute()) {
            unset($model);
            return true;
        } else {
            return false;
        }
    }
}