<?php

namespace App\Services;

class AccountService
{
    private $dataFile = 'accounts.json';

    public function __construct()
    {
        if (!file_exists($this->dataFile)) {
            file_put_contents($this->dataFile, json_encode([]));
        }
    }

    private function loadAccounts()
    {
        $accounts = json_decode(file_get_contents($this->dataFile), true);
        return $accounts ?: [];
    }

    private function saveAccounts($accounts)
    {
        file_put_contents($this->dataFile, json_encode($accounts));
    }

    public function reset()
    {
        $this->saveAccounts([]);
    }

    public function deposit($accountId, $amount)
    {
        $accounts = $this->loadAccounts();
        
        if (!isset($accounts[$accountId])) {
            $accounts[$accountId] = 0;
        }

        $accounts[$accountId] += $amount;

        $this->saveAccounts($accounts);

        return [
            'destination' => [
                'id' => $accountId,
                'balance' => $accounts[$accountId],
            ]
        ];
    }

    public function withdraw($accountId, $amount)
    {
        $accounts = $this->loadAccounts();

        if (!isset($accounts[$accountId])) {
            throw new \Exception(0);
        }

        if ($accounts[$accountId] < $amount) {
            throw new \Exception('Insufficient funds');
        }

        $accounts[$accountId] -= $amount;

        $this->saveAccounts($accounts);

        return [
            'origin' => [
                'id' => $accountId,
                'balance' => $accounts[$accountId],
            ]
        ];
    }

    public function transfer($fromAccountId, $toAccountId, $amount)
    {
        $accounts = $this->loadAccounts();

        if (!isset($accounts[$fromAccountId])) {
            throw new \Exception(0);
        }

        if (!isset($accounts[$toAccountId])) {
            $accounts[$toAccountId] = 0;
        }

        if ($accounts[$fromAccountId] < $amount) {
            throw new \Exception('Insufficient funds in origin account');
        }

        $accounts[$fromAccountId] -= $amount;
        $accounts[$toAccountId] += $amount;

        $this->saveAccounts($accounts);

        return [
            'origin' => [
                'id' => $fromAccountId,
                'balance' => $accounts[$fromAccountId],
            ],
            'destination' => [
                'id' => $toAccountId,
                'balance' => $accounts[$toAccountId],
            ]
        ];
    }

    public function getBalance($accountId)
    {
        $accounts = $this->loadAccounts();
        return $accounts[$accountId] ?? null;
    }
}
