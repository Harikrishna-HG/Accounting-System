<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleAndPermissionSeeder extends Seeder
{
    private const LEGACY_PERMISSION_SLUGS = [
        'news.create',
        'news.edit',
        'news.delete',
        'news.publish',
        'categories.manage',
        'tags.manage',
        'ads.manage',
    ];

    private const LEGACY_ROLE_SLUGS = [
        'editor',
        'author',
    ];

    public function run(): void
    {
        $this->purgeLegacyNewsRecords();

        $permissions = [
            ['name' => 'उत्पादन व्यवस्थापन', 'slug' => 'products.manage', 'description' => 'उत्पादनहरू व्यवस्थापन गर्न सक्ने'],
            ['name' => 'ग्राहक व्यवस्थापन', 'slug' => 'clients.manage', 'description' => 'ग्राहकहरू व्यवस्थापन गर्न सक्ने'],
            ['name' => 'आपूर्तिकर्ता व्यवस्थापन', 'slug' => 'suppliers.manage', 'description' => 'आपूर्तिकर्ताहरू व्यवस्थापन गर्न सक्ने'],
            ['name' => 'बिल व्यवस्थापन', 'slug' => 'invoices.manage', 'description' => 'बिलहरू सिर्जना र सम्पादन गर्न सक्ने'],
            ['name' => 'भुक्तानी व्यवस्थापन', 'slug' => 'payments.manage', 'description' => 'भुक्तानीहरू दर्ता गर्न सक्ने'],
            ['name' => 'खर्च व्यवस्थापन', 'slug' => 'expenses.manage', 'description' => 'खर्चहरू व्यवस्थापन गर्न सक्ने'],
            ['name' => 'खरिद आदेश व्यवस्थापन', 'slug' => 'purchase-orders.manage', 'description' => 'खरिद आदेशहरू व्यवस्थापन गर्न सक्ने'],
            ['name' => 'प्रतिवेदन हेर्ने', 'slug' => 'reports.view', 'description' => 'प्रतिवेदनहरू हेर्न र डाउनलोड गर्न सक्ने'],
            ['name' => 'प्रयोगकर्ता व्यवस्थापन', 'slug' => 'users.manage', 'description' => 'प्रयोगकर्ताहरू व्यवस्थापन गर्न सक्ने'],
            ['name' => 'भूमिका व्यवस्थापन', 'slug' => 'roles.manage', 'description' => 'भूमिका र अनुमतिहरू व्यवस्थापन गर्न सक्ने'],
        ];

        foreach ($permissions as $perm) {
            Permission::updateOrCreate(['slug' => $perm['slug']], $perm);
        }

        $accountingPermissions = [
            'products.manage', 'clients.manage', 'suppliers.manage', 'invoices.manage',
            'payments.manage', 'expenses.manage', 'purchase-orders.manage', 'reports.view',
        ];

        $roles = [
            'super-admin' => [
                'name' => 'सुपर प्रशासक',
                'description' => 'पूर्ण पहुँच भएको प्रशासक',
                'permissions' => array_merge($accountingPermissions, ['users.manage', 'roles.manage']),
            ],
            'admin' => [
                'name' => 'प्रशासक',
                'description' => 'लेखा कार्यहरू र प्रयोगकर्ता व्यवस्थापन',
                'permissions' => array_merge($accountingPermissions, ['users.manage']),
            ],
            'accountant' => [
                'name' => 'लेखापाल',
                'description' => 'बिल, भुक्तानी र खर्च दर्ता गर्न सक्ने',
                'permissions' => ['products.manage', 'clients.manage', 'suppliers.manage', 'invoices.manage', 'payments.manage', 'expenses.manage', 'purchase-orders.manage', 'reports.view'],
            ],
            'auditor' => [
                'name' => 'लेखापरीक्षक',
                'description' => 'प्रतिवेदन र लेखा अभिलेख समीक्षा गर्न सक्ने',
                'permissions' => ['reports.view', 'invoices.manage'],
            ],
            'user' => [
                'name' => 'प्रयोगकर्ता',
                'description' => 'सामान्य प्रयोगकर्ता',
                'permissions' => [],
            ],
        ];

        foreach ($roles as $slug => $roleData) {
            $role = Role::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $roleData['name'],
                    'description' => $roleData['description'],
                ]
            );

            $permIds = Permission::whereIn('slug', $roleData['permissions'])->pluck('id')->toArray();
            $role->permissions()->sync($permIds);
        }
    }

    private function purgeLegacyNewsRecords(): void
    {
        Permission::whereIn('slug', self::LEGACY_PERMISSION_SLUGS)->delete();

        Role::whereIn('slug', self::LEGACY_ROLE_SLUGS)->delete();
    }
}
