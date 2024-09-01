<?php

use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../controllers/EventController.php';
require_once __DIR__ . '/../database/Database.php';

class EventControllerTest extends TestCase {

    private $db;
    private $eventController;

    protected function setUp(): void {
        $this->db = new Database();
        $this->eventController = new EventController($this->db);
    }

    public function testDepositEvent() {
        $event = [
            'type' => 'deposit',
            'account_id' => '1',
            'amount' => 100
        ];

        $this->expectOutputString(json_encode(['balance' => 100]));
        $this->eventController->handleEvent($event);

        $account = $this->db->get('1');
        $this->assertEquals(100, $account->getBalance());
    }

    public function testWithdrawEvent() {
        $account = new Account('1', 100);
        $this->db->set('1', $account);

        $event = [
            'type' => 'withdraw',
            'account_id' => '1',
            'amount' => 50
        ];

        $this->expectOutputString(json_encode(['balance' => 50]));
        $this->eventController->handleEvent($event);
    }

    public function testTransferEvent() {
        $source = new Account('1', 100);
        $destination = new Account('2', 50);
        $this->db->set('1', $source);
        $this->db->set('2', $destination);

        $event = [
            'type' => 'transfer',
            'account_id' => '1',
            'destination_id' => '2',
            'amount' => 25
        ];

        $this->expectOutputString(json_encode([
            'source_balance' => 75,
            'destination_balance' => 75
        ]));
        $this->eventController->handleEvent($event);
    }
}
