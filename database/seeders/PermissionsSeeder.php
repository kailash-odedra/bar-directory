<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin Role
        $adminRole = Role::firstOrCreate(
            ['slug' => 'admin'],
            [
                'name' => 'Admin',
                'description' => 'Administrator with full access to all modules',
                'is_active' => true,
            ]
        );

        // Define all permissions by module
        $permissions = [
            // Dashboard
            ['name' => 'Dashboard Module', 'slug' => 'dashboard-module', 'group' => 'Dashboard'],
            ['name' => 'View Dashboard', 'slug' => 'dashboard-view', 'group' => 'Dashboard'],
            ['name' => 'View Analytics', 'slug' => 'analytics-view', 'group' => 'Dashboard'],
            ['name' => 'View Sales', 'slug' => 'sales-view', 'group' => 'Dashboard'],

            // Bar Module
            ['name' => 'Bar Module', 'slug' => 'bar-module', 'group' => 'Bar'],
            ['name' => 'View Bars', 'slug' => 'bar-view', 'group' => 'Bar'],
            ['name' => 'Create Bars', 'slug' => 'bar-create', 'group' => 'Bar'],
            ['name' => 'Edit Bars', 'slug' => 'bar-edit', 'group' => 'Bar'],
            ['name' => 'Delete Bars', 'slug' => 'bar-delete', 'group' => 'Bar'],

            // Bar Tags
            ['name' => 'View Tags', 'slug' => 'bar-tags-view', 'group' => 'Bar'],
            ['name' => 'Create Tags', 'slug' => 'bar-tags-create', 'group' => 'Bar'],
            ['name' => 'Edit Tags', 'slug' => 'bar-tags-edit', 'group' => 'Bar'],
            ['name' => 'Delete Tags', 'slug' => 'bar-tags-delete', 'group' => 'Bar'],

            // Events
            ['name' => 'View Events', 'slug' => 'events-view', 'group' => 'Bar'],
            ['name' => 'Create Events', 'slug' => 'events-create', 'group' => 'Bar'],
            ['name' => 'Edit Events', 'slug' => 'events-edit', 'group' => 'Bar'],
            ['name' => 'Delete Events', 'slug' => 'events-delete', 'group' => 'Bar'],

            // Bookings
            ['name' => 'View Bookings', 'slug' => 'bookings-view', 'group' => 'Bar'],
            ['name' => 'Create Bookings', 'slug' => 'bookings-create', 'group' => 'Bar'],
            ['name' => 'Edit Bookings', 'slug' => 'bookings-edit', 'group' => 'Bar'],
            ['name' => 'Delete Bookings', 'slug' => 'bookings-delete', 'group' => 'Bar'],

            // Claims
            ['name' => 'View Claims', 'slug' => 'claims-view', 'group' => 'Bar'],
            ['name' => 'Create Claims', 'slug' => 'claims-create', 'group' => 'Bar'],
            ['name' => 'Edit Claims', 'slug' => 'claims-edit', 'group' => 'Bar'],
            ['name' => 'Delete Claims', 'slug' => 'claims-delete', 'group' => 'Bar'],

            // Reviews
            ['name' => 'View Reviews', 'slug' => 'reviews-view', 'group' => 'Bar'],
            ['name' => 'Create Reviews', 'slug' => 'reviews-create', 'group' => 'Bar'],
            ['name' => 'Edit Reviews', 'slug' => 'reviews-edit', 'group' => 'Bar'],
            ['name' => 'Delete Reviews', 'slug' => 'reviews-delete', 'group' => 'Bar'],

            // Menu Categories
            ['name' => 'View Menu Categories', 'slug' => 'menu-categories-view', 'group' => 'Bar'],
            ['name' => 'Create Menu Categories', 'slug' => 'menu-categories-create', 'group' => 'Bar'],
            ['name' => 'Edit Menu Categories', 'slug' => 'menu-categories-edit', 'group' => 'Bar'],
            ['name' => 'Delete Menu Categories', 'slug' => 'menu-categories-delete', 'group' => 'Bar'],

            // Menu Items
            ['name' => 'View Menu Items', 'slug' => 'menu-items-view', 'group' => 'Bar'],
            ['name' => 'Create Menu Items', 'slug' => 'menu-items-create', 'group' => 'Bar'],
            ['name' => 'Edit Menu Items', 'slug' => 'menu-items-edit', 'group' => 'Bar'],
            ['name' => 'Delete Menu Items', 'slug' => 'menu-items-delete', 'group' => 'Bar'],

            // Geographic Module
            ['name' => 'Geographic Module', 'slug' => 'geo-module', 'group' => 'Geographic'],
            ['name' => 'View Countries', 'slug' => 'countries-view', 'group' => 'Geographic'],
            ['name' => 'Create Countries', 'slug' => 'countries-create', 'group' => 'Geographic'],
            ['name' => 'Edit Countries', 'slug' => 'countries-edit', 'group' => 'Geographic'],
            ['name' => 'Delete Countries', 'slug' => 'countries-delete', 'group' => 'Geographic'],

            ['name' => 'View States', 'slug' => 'states-view', 'group' => 'Geographic'],
            ['name' => 'Create States', 'slug' => 'states-create', 'group' => 'Geographic'],
            ['name' => 'Edit States', 'slug' => 'states-edit', 'group' => 'Geographic'],
            ['name' => 'Delete States', 'slug' => 'states-delete', 'group' => 'Geographic'],

            ['name' => 'View Cities', 'slug' => 'cities-view', 'group' => 'Geographic'],
            ['name' => 'Create Cities', 'slug' => 'cities-create', 'group' => 'Geographic'],
            ['name' => 'Edit Cities', 'slug' => 'cities-edit', 'group' => 'Geographic'],
            ['name' => 'Delete Cities', 'slug' => 'cities-delete', 'group' => 'Geographic'],

            ['name' => 'View Regions', 'slug' => 'regions-view', 'group' => 'Geographic'],
            ['name' => 'Create Regions', 'slug' => 'regions-create', 'group' => 'Geographic'],
            ['name' => 'Edit Regions', 'slug' => 'regions-edit', 'group' => 'Geographic'],
            ['name' => 'Delete Regions', 'slug' => 'regions-delete', 'group' => 'Geographic'],

            // CMS Module
            ['name' => 'CMS Module', 'slug' => 'cms-module', 'group' => 'CMS'],
            ['name' => 'View Sections', 'slug' => 'sections-view', 'group' => 'CMS'],
            ['name' => 'Create Sections', 'slug' => 'sections-create', 'group' => 'CMS'],
            ['name' => 'Edit Sections', 'slug' => 'sections-edit', 'group' => 'CMS'],
            ['name' => 'Delete Sections', 'slug' => 'sections-delete', 'group' => 'CMS'],

            // Auth Module
            ['name' => 'Auth Module', 'slug' => 'auth-module', 'group' => 'Auth'],
            ['name' => 'View Users', 'slug' => 'users-view', 'group' => 'Auth'],
            ['name' => 'Create Users', 'slug' => 'users-create', 'group' => 'Auth'],
            ['name' => 'Edit Users', 'slug' => 'users-edit', 'group' => 'Auth'],
            ['name' => 'Delete Users', 'slug' => 'users-delete', 'group' => 'Auth'],

            ['name' => 'View Roles', 'slug' => 'roles-view', 'group' => 'Auth'],
            ['name' => 'Create Roles', 'slug' => 'roles-create', 'group' => 'Auth'],
            ['name' => 'Edit Roles', 'slug' => 'roles-edit', 'group' => 'Auth'],
            ['name' => 'Delete Roles', 'slug' => 'roles-delete', 'group' => 'Auth'],

            ['name' => 'View Permissions', 'slug' => 'permissions-view', 'group' => 'Auth'],
            ['name' => 'Create Permissions', 'slug' => 'permissions-create', 'group' => 'Auth'],
            ['name' => 'Edit Permissions', 'slug' => 'permissions-edit', 'group' => 'Auth'],
            ['name' => 'Delete Permissions', 'slug' => 'permissions-delete', 'group' => 'Auth'],
        ];

        // Create all permissions
        $permissionIds = [];
        foreach ($permissions as $permission) {
            $perm = Permission::firstOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
            $permissionIds[] = $perm->id;
        }

        // Assign all permissions to admin role
        $adminRole->permissions()->sync($permissionIds);

        // Assign admin role to the default admin user
        $adminUser = User::where('email', 'admin@admin.com')->first();
        if ($adminUser) {
            $adminUser->roles()->syncWithoutDetaching([$adminRole->id]);
        }

        $this->command->info('Permissions and Admin role created successfully!');
        $this->command->info('Total permissions created: ' . count($permissionIds));
    }
}
