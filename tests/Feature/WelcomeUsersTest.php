<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WelcomeUsersTest extends TestCase
{
    /** @test */
    function it_welcomes_users_with_nickname()
    {
        $this->get('saludo/edwin/chapako')
            ->assertStatus(200)
            ->assertSee('Bienvenido Edwin, tu apodo es chapako');
    }

    /** @test */
    function it_welcomes_users_without_nickname()
    {
        $this->get('saludo/edwin')
            ->assertStatus(200)
            ->assertSee('Bienvenido Edwin');
    }
}
