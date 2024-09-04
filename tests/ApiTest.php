<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Controllers\EventController;

class ApiTest extends TestCase
{
    protected $controller;

    protected function setUp(): void
    {
        $this->controller = new EventController();
        $this->controller->reset();
    }

    public function testDepositCreatesAccount()
    {
        [$status, $response] = $this->controller->event([
            'type' => 'deposit',
            'destination' => '1000',
            'amount' => 500
        ]);

        $this->assertEquals(201, $status);
        $this->assertIsArray($response);
        $this->assertArrayHasKey('destination', $response);
        $this->assertEquals('1000', $response['destination']['id']);
        $this->assertEquals(500, $response['destination']['balance']);
    }

    public function testWithdrawFromExistingAccount()
    {
        $this->controller->event([
            'type' => 'deposit',
            'destination' => '1000',
            'amount' => 500
        ]);

        [$status, $response] = $this->controller->event([
            'type' => 'withdraw',
            'origin' => '1000',
            'amount' => 200
        ]);

        $this->assertEquals(201, $status);
        $this->assertIsArray($response);
        $this->assertArrayHasKey('origin', $response);
        $this->assertEquals('1000', $response['origin']['id']);
        $this->assertEquals(300, $response['origin']['balance']);
    }

    public function testWithdrawFromNonExistentAccount()
    {
        [$status, $response, $json] = $this->controller->event([
            'type' => 'withdraw',
            'origin' => '9999',
            'amount' => 200
        ]);

        $this->assertEquals(404, $status);
    }

    public function testTransferBetweenAccounts()
    {
        $this->controller->event([
            'type' => 'deposit',
            'destination' => '1000',
            'amount' => 500
        ]);

        [$status, $response] = $this->controller->event([
            'type' => 'transfer',
            'origin' => '1000',
            'destination' => '2000',
            'amount' => 300
        ]);

        $this->assertEquals(201, $status);
        $this->assertIsArray($response);
        $this->assertArrayHasKey('origin', $response);
        $this->assertArrayHasKey('destination', $response);
        $this->assertEquals(200, $response['origin']['balance']);
        $this->assertEquals(300, $response['destination']['balance']);
    }

    public function testGetBalance()
    {
        $this->controller->event([
            'type' => 'deposit',
            'destination' => '1000',
            'amount' => 500
        ]);

        [$status, $response] = $this->controller->balance('1000');

        $this->assertEquals(200, $status);
    }

    public function testGetBalanceForNonExistentAccount()
    {
        [$status, $response] = $this->controller->balance('9999');
    
        $this->assertEquals(404, $status);
    }
    
}