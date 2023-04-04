<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class TransactionsTest extends TestCase
{
    /**
     * test migration.
     */
    public function test_migration(): void
    {
        $response = $this->call('GET', 'api/v1/user-transactions/migrate', []);

        $response->assertStatus($response->status());
    }

    public function test_get_transactions(): void
    {
        $response = $this->call('GET', 'api/v1/user-transactions/transactions', []);

        $response->assertStatus($response->status());
    }

    public function test_get_users(): void
    {
        $response = $this->call('GET', 'api/v1/user-transactions/users', []);

        $response->assertStatus($response->status());
    }
}
