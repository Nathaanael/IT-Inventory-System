<?php

use App\Models\Inventory;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

function pingTarget(string $ip): Inventory
{
    return Inventory::create([
        'nama_user' => 'PC ' . $ip,
        'ip_address' => $ip,
    ]);
}

beforeEach(function () {
    // Skema minimal: migration drop_switch_id tidak bisa berjalan di SQLite,
    // sehingga RefreshDatabase belum bisa dipakai di test.
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->timestamp('email_verified_at')->nullable();
        $table->string('password')->nullable();
        $table->string('role');
        $table->string('vault_pin')->nullable();
        $table->rememberToken();
        $table->timestamps();
    });
    Schema::create('inventories', function (Blueprint $table) {
        $table->id();
        $table->string('nama_user');
        $table->string('ip_address');
        $table->timestamps();
    });

    $this->actingAs(User::factory()->create([
        'role' => 'IT Support',
        'vault_pin' => Hash::make('123456'),
    ]));
});

it('returns the reply line and round trip time for a reachable host', function () {
    $response = $this->getJson(route('inventory.ping', pingTarget('127.0.0.1')));

    $response->assertOk()->assertJsonPath('status', 'online');
    expect($response->json('reply'))->toContain('127.0.0.1')->toMatch('/ttl=/i');
    expect($response->json('time'))->toBeNumeric();
});

it('returns the failure reason for an unreachable host', function () {
    // 192.0.2.0/24 (TEST-NET-1) tidak pernah merespons.
    $response = $this->getJson(route('inventory.ping', pingTarget('192.0.2.1')));

    $response->assertOk()
        ->assertJsonPath('status', 'offline')
        ->assertJsonPath('time', null);
    expect($response->json('reply'))->toBeString()->not->toBeEmpty();
});

it('allows continuous pinging without consuming the vault pin throttle', function () {
    $inventory = pingTarget('127.0.0.1');

    // Melebihi throttle:10,1 yang lama dan throttle:5,1 milik verify-pin.
    foreach (range(1, 11) as $i) {
        $this->getJson(route('inventory.ping', $inventory))->assertOk();
    }

    $this->postJson(route('inventory.vault.verify-pin'), ['pin' => '123456'])->assertOk();
});
