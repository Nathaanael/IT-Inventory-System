<?php

use App\Models\User;
use App\Services\MfaService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    Schema::dropIfExists('activity_logs');
    Schema::dropIfExists('users');

    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('id_karyawan')->unique();
        $table->string('username_ad')->unique();
        $table->string('name');
        $table->string('email')->nullable()->unique();
        $table->string('role');
        $table->string('password')->nullable();
        $table->string('vault_pin')->nullable();
        $table->text('mfa_secret')->nullable();
        $table->timestamp('mfa_enabled_at')->nullable();
        $table->unsignedBigInteger('mfa_last_used_at')->nullable();
        $table->rememberToken();
        $table->timestamps();
    });

    Schema::create('activity_logs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
        $table->string('action');
        $table->text('description');
        $table->string('ip_address')->nullable();
        $table->timestamps();
    });
});

function mfaUser(array $attributes = []): User
{
    return User::create(array_merge([
        'id_karyawan' => fake()->unique()->numerify('######'),
        'username_ad' => fake()->unique()->userName(),
        'name' => fake()->name(),
        'role' => 'IT Support',
        'password' => Hash::make('Password123!'),
    ], $attributes));
}

it('requires a user without MFA to enroll after password login', function () {
    $user = mfaUser(['username_ad' => 'new.user']);

    $this->post(route('login.submit'), [
        'username_ad' => $user->username_ad,
        'password' => 'Password123!',
    ])->assertRedirect(route('mfa.setup'))
        ->assertSessionHas('mfa.pending_user_id', $user->id);

    $this->get(route('mfa.setup'))
        ->assertOk()
        ->assertSee('Hubungkan Microsoft Authenticator');

    $secret = session('mfa.setup_secret');
    $code = app(MfaService::class)->currentCode($secret);

    $this->post(route('mfa.setup.verify'), ['code' => $code])
        ->assertRedirect(route('inventory.index'));

    $this->assertAuthenticatedAs($user);
    expect($user->fresh()->mfa_secret)->toBe($secret)
        ->and($user->fresh()->mfa_enabled_at)->not->toBeNull();
});

it('requires and verifies an authenticator code on every login', function () {
    $secret = app(MfaService::class)->generateSecret();
    $user = mfaUser();
    $user->forceFill([
        'mfa_secret' => $secret,
        'mfa_enabled_at' => now(),
    ])->save();

    $this->post(route('login.submit'), [
        'username_ad' => $user->username_ad,
        'password' => 'Password123!',
    ])->assertRedirect(route('mfa.setup'));

    $this->assertGuest();

    $code = app(MfaService::class)->currentCode($secret);
    $this->post(route('mfa.challenge.verify'), ['code' => $code])
        ->assertRedirect(route('inventory.index'));

    $this->assertAuthenticatedAs($user);
    expect($user->fresh()->mfa_last_used_at)->not->toBeNull();
});

it('rejects reuse of the same authenticator code', function () {
    $secret = app(MfaService::class)->generateSecret();
    $user = mfaUser();
    $user->forceFill([
        'mfa_secret' => $secret,
        'mfa_enabled_at' => now(),
    ])->save();

    $code = app(MfaService::class)->currentCode($secret);

    $this->withSession(['mfa.pending_user_id' => $user->id])
        ->post(route('mfa.challenge.verify'), ['code' => $code])
        ->assertRedirect(route('inventory.index'));

    $this->post(route('logout'));

    $this->withSession(['mfa.pending_user_id' => $user->id])
        ->post(route('mfa.challenge.verify'), ['code' => $code])
        ->assertSessionHasErrors('code');
});

it('allows a super admin to unlink another users MFA and records an audit log', function () {
    $admin = mfaUser(['role' => 'Super Admin']);
    $user = mfaUser();
    $user->forceFill([
        'mfa_secret' => app(MfaService::class)->generateSecret(),
        'mfa_enabled_at' => now(),
    ])->save();

    $this->actingAs($admin)
        ->withSession(['mfa.verified_user_id' => $admin->id])
        ->post(route('master.users.unlink-mfa', $user))
        ->assertRedirect(route('master.users.index'));

    expect($user->fresh()->mfa_secret)->toBeNull()
        ->and($user->fresh()->mfa_enabled_at)->toBeNull();

    $this->assertDatabaseHas('activity_logs', [
        'user_id' => $admin->id,
        'action' => 'update',
    ]);
});
