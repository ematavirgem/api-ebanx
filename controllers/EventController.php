<?php

require_once 'models/Account.php';
require_once 'database/Database.php';

class EventController {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function handleEvent($event) {
        $event = $this->sanitizeEvent($event);

        $type = $event['type'] ?? null;
        $accountId = $event['account_id'] ?? null;
        $amount = $event['amount'] ?? 0;

        if ($type === 'deposit' || $type === 'withdraw') {
            $this->processTransaction($type, $accountId, $amount);
        } elseif ($type === 'transfer') {
            $this->processTransfer($accountId, $event['destination_id'], $amount);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid event type']);
        }
    }

    private function processTransaction($type, $accountId, $amount) {
        $account = $this->db->get($accountId);

        if ($account === null) {
            $account = new Account($accountId, 0);
            $this->db->set($accountId, $account);
        }

        try {
            if ($type === 'deposit') {
                $account->deposit($amount);
            } elseif ($type === 'withdraw') {
                $account->withdraw($amount);
            }
            echo json_encode(['balance' => $account->getBalance()]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    private function processTransfer($sourceId, $destinationId, $amount) {
        $source = $this->db->get($sourceId);
        $destination = $this->db->get($destinationId);

        if ($source === null || $destination === null) {
            http_response_code(404);
            echo json_encode(['error' => 'Source or destination account not found']);
            return;
        }

        try {
            $source->withdraw($amount);
            $destination->deposit($amount);
            echo json_encode([
                'source_balance' => $source->getBalance(),
                'destination_balance' => $destination->getBalance()
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    private function sanitizeEvent($event) {
        return array_map(function($item) {
            return htmlspecialchars(strip_tags(trim($item)));
        }, $event);
    }
}
