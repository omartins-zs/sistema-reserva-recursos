<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_public_home_page_loads_the_reservation_interface(): void
    {
        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertSee('Reserva de recursos corporativos');
    }
}
