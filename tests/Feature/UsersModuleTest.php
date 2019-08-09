<?php

namespace Tests\Feature;

use App\Profession;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UsersModuleTest extends TestCase
{
    use RefreshDatabase;

    protected $profession;

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

        $this->get("/usuarios/{$user->id}")
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
        $profession = factory(Profession::class)->create();

        $this->get('usuarios/nuevo')
            ->assertStatus(200)
            ->assertSee('Crear usuario')
            ->assertViewHas('professions', function ($professions) use ($profession){
                return $professions->contains($profession);
            });
    }

    /** @test */
    function it_creates_a_new_user()
    {
        $this->withoutExceptionHandling();

        $this->post('/usuarios', $this->getValidData())->assertRedirect(route('users'));

        $this->assertCredentials([
           'name' => 'Edwin',
           'email' => 'edwin.ibanez@tooducks.com',
           'password' => '123456',
        ]);

        $this->assertDatabaseHas('user_profiles', [
           'bio' => 'Programador de Laravel y Vue.js',
           'twitter' => 'https://twitter.com/elchapako',
           'user_id' => User::findByEmail('edwin.ibanez@tooducks.com')->id,
            'profession_id' => $this->profession->id,
        ]);
    }

    /** @test */
    function the_twitter_field_is_optional()
    {
        $this->withoutExceptionHandling();

        $this->post('/usuarios', $this->getValidData([
            'twitter' => null,
        ]))->assertRedirect(route('users'));

        $this->assertCredentials([
            'name' => 'Edwin',
            'email' => 'edwin.ibanez@tooducks.com',
            'password' => '123456',
        ]);

        $this->assertDatabaseHas('user_profiles', [
            'bio' => 'Programador de Laravel y Vue.js',
            'twitter' => null,
            'user_id' => User::findByEmail('edwin.ibanez@tooducks.com')->id,
        ]);
    }

    /** @test */
    function the_profession_id_field_is_optional()
    {
        $this->withoutExceptionHandling();

        $this->post('/usuarios', $this->getValidData([
            'profession_id' => null
        ]))->assertRedirect(route('users'));

        $this->assertCredentials([
            'name' => 'Edwin',
            'email' => 'edwin.ibanez@tooducks.com',
            'password' => '123456',
        ]);

        $this->assertDatabaseHas('user_profiles', [
            'bio' => 'Programador de Laravel y Vue.js',
            'user_id' => User::findByEmail('edwin.ibanez@tooducks.com')->id,
            'profession_id' => null,
        ]);
    }

    /** @test */
    function the_name_is_required()
    {
        $this->from('usuarios/nuevo')
            ->post('/usuarios', $this->getValidData([
                'name' => '',
            ]))->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['name' => 'El campo nombre es obligatorio']);

        $this->assertDatabaseEmpty('users');

    }

    /** @test */
    function the_email_is_required()
    {
        $this->from('usuarios/nuevo')
            ->post('/usuarios', $this->getValidData([
                'email' => '',
            ]))->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['email' => 'El campo email es obligatorio']);

        $this->assertDatabaseEmpty('users');

    }

    /** @test */
    function the_email_must_be_valid()
    {
        //$this->withoutExceptionHandling();

        $this->from('usuarios/nuevo')
            ->post('/usuarios', $this->getValidData([
                'email' => 'correo-no-valido',
            ]))->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['email']);

        $this->assertDatabaseEmpty('users');

    }

    /** @test */
    function the_email_must_be_unique()
    {
        factory(User::class)->create([
           'email' => 'edwin.ibanez@tooducks.com'
        ]);

        $this->from('usuarios/nuevo')
            ->post('/usuarios', $this->getValidData([
                'email' => 'edwin.ibanez@tooducks.com'
            ]))->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['email']);

        $this->assertEquals(1, User::count());

    }

    /** @test */
    function the_password_is_required()
    {
        $this->from('usuarios/nuevo')
            ->post('/usuarios', $this->getValidData([
                'password' => '',
            ]))->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['password' => 'El campo password es obligatorio']);

        $this->assertDatabaseEmpty('users');

    }

    /** @test */
    function the_profession_must_be_valid()
    {
        $this->handleValidationExceptions();

        $this->from('usuarios/nuevo')
            ->post('/usuarios', $this->getValidData([
                'profession_id' => '999',
            ]))->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['profession_id']);

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function only_not_deleted_professions_can_be_selected()
    {
        $deletedProfession = factory(Profession::class)->create([
           'deleted_at' => now()->format('Y-m-d'),
        ]);
        $this->handleValidationExceptions();

        $this->from('usuarios/nuevo')
            ->post('/usuarios', $this->getValidData([
                'profession_id' => $deletedProfession->id,
            ]))->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['profession_id']);

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function it_loads_the_edit_user_page()
    {
        //$this->withoutExceptionHandling();

        $user = factory(User::class)->create();

        $this->get("/usuarios/{$user->id}/editar")
            ->assertStatus(200)
            ->assertViewIs('users.edit')
            ->assertSee('Editar Usuario')
            ->assertViewHas('user', function ($viewUser) use ($user){
                return $viewUser->id === $user->id;
            });
    }

    /** @test */
    function it_updates_a_user()
    {
        $user = factory(User::class)->create();

        //$this->withoutExceptionHandling();

        $this->put("/usuarios/{$user->id}", [
            'name' => 'Edwin',
            'email' => 'edwin.ibanez@tooducks.com',
            'password' => '123456'
        ])->assertRedirect("/usuarios/{$user->id}");

        $this->assertCredentials([
            'name' => 'Edwin',
            'email' => 'edwin.ibanez@tooducks.com',
            'password' => '123456',
        ]);
    }

    /** @test */
    function the_name_is_required_when_updating_the_user()
    {
        //$this->withoutExceptionHandling();
        $user = factory(User::class)->create();

        $this->from("/usuarios/{$user->id}/editar")
            ->put("/usuarios/{$user->id}", [
                'name' => '',
                'email' => 'edwin.ibanez@tooducks.com',
                'password' => '123456'
        ])
            ->assertRedirect("/usuarios/{$user->id}/editar")
            ->assertSessionHasErrors(['name']);

        $this->assertDatabaseMissing('users', ['email' => 'edwin.ibanez@tooducks.com']);

    }

    /** @test */
    function the_email_must_be_valid_when_updating_the_user()
    {
        //$this->withoutExceptionHandling();
        $user = factory(User::class)->create();

        $this->from("/usuarios/{$user->id}/editar")
            ->put("/usuarios/{$user->id}", [
                'name' => 'Edwin Ibañez',
                'email' => 'correo-no-valido',
                'password' => '123456'
            ])
            ->assertRedirect("/usuarios/{$user->id}/editar")
            ->assertSessionHasErrors(['email']);

        $this->assertDatabaseMissing('users', ['name' => 'Edwin Ibañez']);
    }

    /** @test */
    function the_email_must_be_unique_when_updating_the_user()
    {
        //$this->withoutExceptionHandling();

        factory(User::class)->create([
           'email' => 'existing-email@example.com'
        ]);

        $user = factory(User::class)->create([
            'email' => 'edwin.ibanez@tooducks.com'
        ]);

        $this->from("usuarios/{$user->id}/editar")
            ->put("usuarios/{$user->id}", [
                'name' => 'Edwin',
                'email' => 'existing-email@example.com',
                'password' => '123456'
            ]   )->assertRedirect("usuarios/{$user->id}/editar")
            ->assertSessionHasErrors(['email']);

        //$this->assertEquals(1, User::count());

    }

    /** @test */
    function the_users_email_can_stay_the_same_when_updating_the_user()
    {
        //$this->withoutExceptionHandling();
        $user = factory(User::class)->create([
            'email' => 'edwin.ibanez@tooducks.com'
        ]);

        $this->from("usuarios/{$user->id}/editar")
            ->put("usuarios/{$user->id}", [
                'name' => 'Edwin',
                'email' => 'edwin.ibanez@tooducks.com',
                'password' => '12345678'
            ])->assertRedirect("usuarios/{$user->id}");

        $this->assertDatabaseHas('users', [
            'name' => 'Edwin',
            'email' => 'edwin.ibanez@tooducks.com'
        ]);
    }

    /** @test */
    function the_password_is_optional_when_updating_the_user()
    {
        $oldpassword = 'clave_anterior';
        $user = factory(User::class)->create([
            'password' => bcrypt($oldpassword),
        ]);

        $this->from("usuarios/{$user->id}/editar")
            ->put("usuarios/{$user->id}", [
                'name' => 'Edwin',
                'email' => 'edwin.ibanez@tooducs.com',
                'password' => '',
            ])->assertRedirect("usuarios/{$user->id}");

        $this->assertCredentials([
            'name' => 'Edwin',
            'email' => 'edwin.ibanez@tooducs.com',
            'password' => $oldpassword,
        ]);

    }

    /** @test */
    function it_deletes_a_user()
    {
        //$this->withoutExceptionHandling();

        $user = factory(User::class)->create();

        $this->delete("usuarios/{$user->id}")
            ->assertRedirect('usuarios');

        $this->assertDatabaseMissing('users', [
           'id' => $user->id
        ]);

        //$this->assertSame(0, User::count());
    }

    protected function getValidData(array $custom = [])
    {
        $this->profession = factory(Profession::class)->create();

        return array_merge([
            'name' => 'Edwin',
            'email' => 'edwin.ibanez@tooducks.com',
            'password' => '123456',
            'profession_id' => $this->profession->id,
            'bio' => 'Programador de Laravel y Vue.js',
            'twitter' => 'https://twitter.com/elchapako'
        ], $custom);
    }

}
