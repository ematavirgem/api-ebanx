<?php

namespace App\Controllers;

use App\Services\AccountService;
use Exception;

class EventController
{
    protected $accountService;

    public function __construct()
    {
        $this->accountService = new AccountService();
    }

    public function reset()
    {
        $this->accountService->reset();
        return [200, 'OK', false];
    }

    public function event($data)
    {
        try {
            [$status, $response] = match ($data['type']) {
                'deposit' => [201, $this->accountService->deposit($data['destination'], $data['amount']), true],
                'withdraw' => [201, $this->accountService->withdraw($data['origin'], $data['amount']), true],
                'transfer' => [201, $this->accountService->transfer($data['origin'], $data['destination'], $data['amount']), true],
                default => [400, ['error' => 'Invalid event type'], true],
            };

            return [$status, $response, true];
        } catch (Exception $e) {
            return [404, $e->getMessage(), false];
        }
    }

    public function balance($accountId)
    {
        $balance = $this->accountService->getBalance($accountId);
        if ($balance !== null) {
            return [200, $balance, false];
        } else {
            return [404, 0, false];
        }
    }

}
