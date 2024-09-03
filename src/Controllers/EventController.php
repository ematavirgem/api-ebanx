<?php

namespace App\Controllers;

use App\Services\AccountService;
use Exception;

class EventController
{
    private $service;

    public function __construct()
    {
        $this->service = new AccountService();
    }

    public function reset()
    {
        try {
            $this->service->reset();
            echo json_encode(['message' => 'State reset']);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function balance($id)
    {
        try {
            $balance = $this->service->getBalance($id);
            if ($balance === null) {
                http_response_code(404);
                echo json_encode(['error' => 'Account not found']);
            } else {
                echo json_encode(['balance' => $balance]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function event($data)
    {
        try {
            $response = match ($data['type']) {
                'deposit' => function() use ($data) {
                    $this->service->deposit($data['destination'], $data['amount']);
                    return json_encode(['destination' => ['id' => $data['destination'], 'balance' => $this->service->getBalance($data['destination'])]]);
                },
                'withdraw' => function() use ($data) {
                    if ($this->service->withdraw($data['origin'], $data['amount'])) {
                        return json_encode(['origin' => ['id' => $data['origin'], 'balance' => $this->service->getBalance($data['origin'])]]);
                    } else {
                        http_response_code(404);
                        return json_encode(['error' => 'Account not found or insufficient funds']);
                    }
                },
                'transfer' => function() use ($data) {
                    if ($this->service->transfer($data['origin'], $data['destination'], $data['amount'])) {
                        return json_encode([
                            'origin' => ['id' => $data['origin'], 'balance' => $this->service->getBalance($data['origin'])],
                            'destination' => ['id' => $data['destination'], 'balance' => $this->service->getBalance($data['destination'])],
                        ]);
                    } else {
                        http_response_code(404);
                        return json_encode(['error' => 'Transfer failed']);
                    }
                },
                default => function() {
                    http_response_code(400);
                    return json_encode(['error' => 'Invalid event type']);
                }
            };

            echo $response();
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
