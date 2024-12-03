<?php

class Controller
{
    protected $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function index() {}
    public function show($id) {}

    public static function EnumAndNull(&$value) {
        if (is_object($value)) {
            if (method_exists($value, '__toString')) {
                $value = (string)$value;
                $value = empty($value) ? null : $value;
            } elseif (property_exists($value, 'value')) {
                $value = (string)$value->value;
                $value = empty($value) ? null : $value;
            } else {
                die('enum or else error');
            }
        } elseif (is_string($value)) {
            $value = empty($value) ? null : $value;
        }
    }
}

?>