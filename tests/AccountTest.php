<?php

use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../models/Account.php';

class AccountTest extends TestCase {

    public function testInitialBalance() {
        $account = new Account('1', 100);
        $this->assertEquals(100, $account->getBalance());
    }

    public function testDeposit() {
        $account = new Account('1', 100);
        $account->deposit(50);
        $this->assertEquals(150, $account->getBalance());
    }

    public function testWithdraw() {
        $account = new Account('1', 100);
        $account->withdraw(50);
        $this->assertEquals(50, $account->getBalance());
    }

    public function testWithdrawMoreThanBalance() {
        $this->expectException(Exception::class);

        $account = new Account('1', 50);
        $account->withdraw(100);
    }
}
