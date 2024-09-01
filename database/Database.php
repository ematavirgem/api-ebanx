<?php

class Database {
    private $data = [];

    public function __construct() {
        // Inicializar com dados se necessário
    }

    public function get($key) {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    public function set($key, $value) {
        $this->data[$key] = $value;
    }
}
