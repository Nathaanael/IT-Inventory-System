<?php

use App\Models\Inventory;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

function searchedIps(array $query): array
{
    return test()->get(route('inventory.index', $query + ['per_page' => 50]))
        ->assertOk()
        ->viewData('inventories')
        ->pluck('ip_address')
        ->sort()
        ->values()
        ->all();
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
    Schema::create('departments', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('unit')->nullable();
        $table->timestamps();
    });
    Schema::create('inventories', function (Blueprint $table) {
        $table->id();
        $table->string('nama_user');
        $table->foreignId('department_id')->nullable();
        $table->foreignId('created_by')->nullable();
        $table->string('ip_address');
        $table->timestamps();
    });

    $this->withoutVite();
    $this->actingAs(User::factory()->create([
        'role' => 'IT Support',
        'vault_pin' => Hash::make('123456'),
    ]));

    foreach (['10.99.27.1', '10.99.27.2', '10.199.27.1', '10.1.99.5', '99.1.2.3', '192.168.1.99', '10.0.27.1'] as $ip) {
        Inventory::create(['nama_user' => 'PC ' . $ip, 'ip_address' => $ip]);
    }
});

it('matches a segment only at the position of its box', function () {
    expect(searchedIps(['ip2' => '99']))->toBe(['10.99.27.1', '10.99.27.2']);
    expect(searchedIps(['ip1' => '99']))->toBe(['99.1.2.3']);
    expect(searchedIps(['ip3' => '99']))->toBe(['10.1.99.5']);
    expect(searchedIps(['ip4' => '99']))->toBe(['192.168.1.99']);
});

it('requires every filled box to match', function () {
    expect(searchedIps(['ip1' => '10', 'ip2' => '99', 'ip4' => '2']))->toBe(['10.99.27.2']);
    expect(searchedIps(['ip1' => '10', 'ip2' => '99', 'ip3' => '27', 'ip4' => '1']))->toBe(['10.99.27.1']);
    expect(searchedIps(['ip1' => '192', 'ip2' => '99']))->toBe([]);
});

it('treats zero as a real segment value', function () {
    expect(searchedIps(['ip2' => '0']))->toBe(['10.0.27.1']);
});

it('strips leading zeros and ignores invalid values', function () {
    expect(searchedIps(['ip2' => '099']))->toBe(['10.99.27.1', '10.99.27.2']);
    expect(searchedIps(['ip2' => '999']))->toBe([]);
    expect(searchedIps(['ip2' => '%']))->toHaveCount(7);
    expect(searchedIps(['ip2' => ['99']]))->toHaveCount(7);
});

it('combines with the general search box', function () {
    Inventory::create(['nama_user' => 'Budi', 'ip_address' => '10.99.30.1']);

    expect(searchedIps(['ip2' => '99', 'search' => 'Budi']))->toBe(['10.99.30.1']);
});

it('keeps the segment boxes filled after a full page load', function () {
    $this->get(route('inventory.index', ['ip2' => '99']))
        ->assertOk()
        ->assertViewHas('ipSegments', [1 => '', 2 => '99', 3 => '', 4 => '']);
});
