<?php

use PHPUnit\Framework\TestCase;
use App\Services\AccountService;

class ApiTest extends TestCase
{
    private $service;

    protected function setUp(): void
    {
        $this->service = new AccountService();
        $this->service->reset();
    }

    public function testCreateAccount()
    {
        $this->service->createAccount(1000, 500);
        $this->assertEquals(500, $this->service->getBalance(1000));
    }

    public function testDeposit()
    {
        $this->service->createAccount(1000);
        $this->service->deposit(1000, 200);
        $this->assertEquals(200, $this->service->getBalance(1000));
    }

    public function testWithdraw()
    {
        $this->service->createAccount(1000, 500);
        $this->service->withdraw(1000, 200);
        $this->assertEquals(300, $this->service->getBalance(1000));
    }

    public function testTransfer()
    {
        $this->service->createAccount(1000, 500);
        $this->service->createAccount(1001, 300);
        $this->service->transfer(1000, 1001, 200);
        $this->assertEquals(300, $this->service->getBalance(1000));
        $this->assertEquals(500, $this->service->getBalance(1001));
    }
}
