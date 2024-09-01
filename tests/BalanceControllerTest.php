<?php

use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../controllers/BalanceController.php';
require_once __DIR__ . '/../database/Database.php';

class BalanceControllerTest extends TestCase {

    private $db;
    private $balanceController;

    protected function setUp(): void {
        $this->db = new Database();
        $this->balanceController = new BalanceController($this->db);
    }

    public function testGetBalanceForNonExistentAccount() {
        $this->expectOutputString(json_encode(['error' => 'Account not found']));
        $this->balanceController->getBalance('1');
        $this->assertEquals(404, http_response_code());
    }

    public function testGetBalanceForExistingAccount() {
        $account = new Account('1', 100);
        $this->db->set('1', $account);

        $this->expectOutputString(json_encode(['balance' => 100]));
        $this->balanceController->getBalance('1');
    }
}
