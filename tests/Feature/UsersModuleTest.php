<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UsersModuleTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    function it_shows_the_users_list()
    {
        factory(User::class)->create([
           'name' => 'Edwin',
        ]);

        factory(User::class)->create([
            'name' => 'Mela'
        ]);

        $this->get('/usuarios')
            ->assertStatus(200)
            ->assertSee('Listado de usuarios')
            ->assertSee('Edwin')
            ->assertSee('Mela');
    }

    /** @test */
    function it_shows_a_default_message_if_the_user_list_is_empty()
    {
        $this->get('/usuarios')
            ->assertStatus(200)
            ->assertSee('No hay usuarios registrados.');
    }

    /** @test */
    function it_displays_the_users_details()
    {
        $user = factory(User::class)->create([
           'name' => 'Edwin Ibañez'
        ]);

        $this->get('usuarios/'.$user->id)
            ->assertStatus(200)
            ->assertSee('Edwin Ibañez');
    }

    /** @test */
    function it_display_a_404_error_if_user_is_not_found()
    {
        $this->get('/usuarios/999')
            ->assertStatus(404)
            ->assertSee('Página no encontrada');
    }

    /** @test */
    function it_loads_the_new_users_page()
    {
        $this->get('usuarios/nuevo')
            ->assertStatus(200)
            ->assertSee('Crear usuario');
    }

    /** @test */
    function it_creates_a_new_user()
    {
        $this->withoutExceptionHandling();

        $this->post('/usuarios', [
           'name' => 'Edwin',
           'email' => 'edwin.ibanez@tooducks.com',
           'password' => '123456'
        ])->assertRedirect(route('users'));

        $this->assertCredentials([
           'name' => 'Edwin',
           'email' => 'edwin.ibanez@tooducks.com',
           'password' => '123456',
        ]);
    }

    /** @test */
    function the_name_is_required()
    {
        $this->from('usuarios/nuevo')
            ->post('/usuarios', [
                'name' => '',
                'email' => 'edwin.ibanez@tooducks.com',
                'password' => '123456'
        ]   )->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['name' => 'El campo nombre es obligatorio']);

        $this->assertEquals(0, User::count());

    //    $this->assertDatabaseMissing('users', [
    //       'email' => 'edwin.ibanez@tooducks.com'
    //    ]);
    }

}
