<?php

namespace App\Models;

class Account
{
    public $id;
    public $balance;

    public function __construct($id, $balance = 0)
    {
        $this->id = $id;
        $this->balance = $balance;
    }
}
