<?php

class Account {
    private $id;
    private $balance;

    public function __construct($id, $balance = 0) {
        $this->id = $id;
        $this->balance = $balance;
    }

    public function getId() {
        return $this->id;
    }

    public function getBalance() {
        return $this->balance;
    }

    public function deposit($amount) {
        $this->balance += $amount;
    }

    public function withdraw($amount) {
        if ($amount > $this->balance) {
            throw new Exception('Insufficient funds');
        }
        $this->balance -= $amount;
    }
}
