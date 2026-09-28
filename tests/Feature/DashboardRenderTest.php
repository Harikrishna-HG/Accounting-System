<?php

use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

function adminUser(): User
{
    return User::factory()->create([
        'role_id' => Role::where('slug', 'super-admin')->firstOrFail()->id,
    ]);
}

function makeProducts(int $count): void
{
    Product::insert(array_map(fn (int $i) => [
        'name' => "Product {$i}",
        'slug' => "product-{$i}",
        'sku' => "SKU-{$i}",
        'price' => 100,
        'cost_price' => 50,
        'stock_quantity' => 10,
        'is_active' => true,
    ], range(1, $count)));
}

function pivotQueryCount(): int
{
    DB::flushQueryLog();
    DB::enableQueryLog();

    test()->actingAs(adminUser())->get('/dashboard/roles')->assertOk();

    $count = collect(DB::getQueryLog())
        ->filter(fn ($q) => str_contains($q['query'], 'permission_role'))
        ->count();

    DB::disableQueryLog();

    return $count;
}

it('renders the dashboard with the product count supplied to the sidebar', function () {
    $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);
    makeProducts(3);

    $this->actingAs(adminUser())
        ->get('/accounting/dashboard')
        ->assertOk()
        ->assertSee('<span class="menu-badge">3</span>', escape: false);
});

it('keeps the product count badge in sync with the products table', function () {
    $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);
    makeProducts(7);

    $this->actingAs(adminUser())
        ->get('/accounting/dashboard')
        ->assertOk()
        ->assertSee('<span class="menu-badge">7</span>', escape: false);
});

it('does not run one permission query per role when listing roles', function () {
    $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);

    $withFiveRoles = pivotQueryCount();

    Role::insert(collect(range(1, 6))->map(fn (int $i) => [
        'name' => "Extra Role {$i}",
        'slug' => "extra-role-{$i}",
    ])->all());

    $withElevenRoles = pivotQueryCount();

    expect($withElevenRoles)->toBe($withFiveRoles);
});
