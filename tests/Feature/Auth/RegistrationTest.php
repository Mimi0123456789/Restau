<?php

namespace Tests\Feature\Auth;

use App\Mail\BienvenueMail;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Empêche l'envoi de vrais emails pendant les tests
        Mail::fake();

        // Création des rôles nécessaires
        Role::create([
            'id' => 1,
            'libelle' => 'Administrateur',
        ]);

        Role::create([
            'id' => 2,
            'libelle' => 'Client',
        ]);

        Role::create([
            'id' => 3,
            'libelle' => 'Employé',
        ]);
    }

    /**
     * Vérifie que la page d'inscription est accessible.
     */
    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    /**
     * Vérifie qu'un nouvel utilisateur peut s'inscrire.
     */
    public function test_new_user_can_register(): void
    {
        $response = $this->post('/register', [
            'prenom' => 'Marie',
            'nom' => 'Martin',
            'telephone' => '0612345678',
            'adresse_postale' => '10 rue de Paris',
            'email' => 'marie.martin@test.fr',

            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        // Aucune erreur de validation
        $response->assertSessionHasNoErrors();

        // L'utilisateur doit être connecté après inscription
        $this->assertAuthenticated();

        // Vérification de l'enregistrement en BDD
        $this->assertDatabaseHas('users', [
            'prenom' => 'Marie',
            'nom' => 'Martin',
            'telephone' => '0612345678',
            'adresse_postale' => '10 rue de Paris',
            'email' => 'marie.martin@test.fr',
            'role_id' => 2,
            'pays' => 'France',
            'is_active' => 1,
        ]);

        // Le contrôleur redirige vers home
        $response->assertRedirect(route('home'));
    }

    /**
     * Vérifie que l'adresse email doit être unique.
     */
    public function test_email_must_be_unique(): void
    {
        User::factory()->create([
            'prenom' => 'Marie',
            'nom' => 'Martin',
            'telephone' => '0612345678',
            'adresse_postale' => '10 rue de Paris',
            'email' => 'marie@test.fr',
            'role_id' => 2,
        ]);

        $response = $this->post('/register', [
            'prenom' => 'Paul',
            'nom' => 'Dupont',
            'telephone' => '0698765432',
            'adresse_postale' => '20 rue Victor Hugo',
            'email' => 'marie@test.fr',

            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertSessionHasErrors('email');

        // Toujours un seul utilisateur avec cet email
        $this->assertEquals(
            1,
            User::where('email', 'marie@test.fr')->count()
        );
    }

    /**
     * Vérifie que la confirmation du mot de passe
     * doit correspondre au mot de passe.
     */
    public function test_password_confirmation_must_match(): void
    {
        $response = $this->post('/register', [
            'prenom' => 'Marie',
            'nom' => 'Martin',
            'telephone' => '0612345678',
            'adresse_postale' => '10 rue de Paris',
            'email' => 'marie@test.fr',

            'password' => 'Password123!',
            'password_confirmation' => 'Password456!',
        ]);

        $response->assertSessionHasErrors('password');

        $this->assertGuest();

        $this->assertDatabaseMissing('users', [
            'email' => 'marie@test.fr',
        ]);
    }

    /**
     * Vérifie que les champs obligatoires sont contrôlés.
     */
    public function test_required_fields_are_validated(): void
    {
        $response = $this->post('/register', []);

        $response->assertSessionHasErrors([
            'prenom',
            'nom',
            'telephone',
            'adresse_postale',
            'email',
            'password',
        ]);

        $this->assertGuest();
    }

    /**
     * Vérifie que le mot de passe respecte les règles
     * de sécurité définies dans le contrôleur.
     */
    public function test_password_must_respect_security_rules(): void
    {
        $response = $this->post('/register', [
            'prenom' => 'Marie',
            'nom' => 'Martin',
            'telephone' => '0612345678',
            'adresse_postale' => '10 rue de Paris',
            'email' => 'marie@test.fr',

            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('password');

        $this->assertGuest();

        $this->assertDatabaseMissing('users', [
            'email' => 'marie@test.fr',
        ]);
    }

    /**
     * Vérifie qu'un nouvel utilisateur reçoit automatiquement
     * le rôle Client (role_id = 2).
     */
    public function test_new_user_receives_client_role(): void
    {
        $this->post('/register', [
            'prenom' => 'Jean',
            'nom' => 'Dupont',
            'telephone' => '0611223344',
            'adresse_postale' => '5 avenue de France',
            'email' => 'jean.dupont@test.fr',

            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'jean.dupont@test.fr',
            'role_id' => 2,
        ]);
    }

    /**
     * Vérifie que l'email de bienvenue est envoyé.
     */
    public function test_welcome_email_is_sent_after_registration(): void
    {
        $this->post('/register', [
            'prenom' => 'Marie',
            'nom' => 'Martin',
            'telephone' => '0612345678',
            'adresse_postale' => '10 rue de Paris',
            'email' => 'marie.martin@test.fr',

            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        Mail::assertSent(BienvenueMail::class, function ($mail) {
            return $mail->hasTo('marie.martin@test.fr');
        });
    }
}