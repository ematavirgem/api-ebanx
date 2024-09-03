<?php

namespace App\Services;

use App\Models\Account;
use Exception;

class AccountService
{
    private $accounts = [];

    public function reset()
    {
        try {
            $this->accounts = [];
        } catch (Exception $e) {
            throw new Exception("Failed to reset accounts: " . $e->getMessage());
        }
    }

    public function createAccount($id, $initialBalance)
    {
        try {
            if (!isset($this->accounts[$id])) {
                $this->accounts[$id] = (object) ['id' => $id, 'balance' => $initialBalance];
            } else {
                throw new Exception("Account already exists.");
            }
        } catch (Exception $e) {
            throw new Exception("Failed to create account: " . $e->getMessage());
        }
    }

    public function getBalance($id)
    {
        try {
            return isset($this->accounts[$id]) ? $this->accounts[$id]->balance : null;
        } catch (Exception $e) {
            throw new Exception("Failed to get balance: " . $e->getMessage());
        }
    }

    public function deposit($id, $amount)
    {
        try {
            $isNewAccount = false;

            if (!isset($this->accounts[$id])) {
                $this->createAccount($id, $amount);
                $isNewAccount = true;
            } else {
                $this->accounts[$id]->balance += $amount;
            }

            $currentBalance = $this->accounts[$id]->balance;

            return $isNewAccount ? 201 : json_encode(['status' => 200, 'balance' => $currentBalance]);
        } catch (Exception $e) {
            throw new Exception("Failed to deposit: " . $e->getMessage());
        }
    }

    public function withdraw($id, $amount)
    {
        try {
            if (!isset($this->accounts[$id]) || $this->accounts[$id]->balance < $amount) {
                return false;
            }
            $this->accounts[$id]->balance -= $amount;
            return true;
        } catch (Exception $e) {
            throw new Exception("Failed to withdraw: " . $e->getMessage());
        }
    }

    public function transfer($fromId, $toId, $amount)
    {
        try {
            if (!isset($this->accounts[$fromId]) || !isset($this->accounts[$toId]) || $this->accounts[$fromId]->balance < $amount) {
                return false;
            }
            $this->accounts[$fromId]->balance -= $amount;
            $this->accounts[$toId]->balance += $amount;
            return true;
        } catch (Exception $e) {
            throw new Exception("Failed to transfer: " . $e->getMessage());
        }
    }
}
