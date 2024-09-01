<?php

require_once 'models/Account.php';
require_once 'database/Database.php';

class BalanceController {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function getBalance($accountId) {
        $accountId = $this->sanitizeInput($accountId);

        $account = $this->db->get($accountId);

        if ($account === null) {
            http_response_code(404);
            echo json_encode(['error' => 'Account not found']);
            return;
        }

        echo json_encode(['balance' => $account->getBalance()]);
    }

    private function sanitizeInput($input) {
        return htmlspecialchars(strip_tags(trim($input)));
    }
}
