<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Start a transaction to ensure all operations succeed or fail together
        DB::transaction(function () {

            $roles = [
                //GeneralRoles
                [ 'name' => 'Super Admin', 'guard_name' => 'user'],
                [ 'name' => 'Admin', 'guard_name' => 'user'],
                [ 'name' => 'Agent', 'guard_name' => 'user'],
            ];
            
            collect($roles)->each(function ($role) {
                Role::firstOrCreate($role);
            });

            $adminRole = Role::where('name','Admin')->first();

            // Define permissions by module
            $userPermissions = $this->getPermissions('user');

            // Create permissions if they don't exist

            foreach ($userPermissions as $permission) {
                Permission::firstOrCreate([
                    'name' => $permission,
                    'guard_name' => 'user'
                ]);
            }

            $adminRole->syncPermissions(Permission::where('guard_name', 'user')->get());

            $this->command->info('admin role created and permissions assigned successfully!');
        });
    }

    /**
     * Get all permissions for the system
     *
     * @return array
     */
    private function getPermissions($guard = 'user'): array
    {
        $generalPermissions = [
            // Role management
            'role-list',
            'role-create',
            'role-view',
            'role-edit',
            'role-delete',
            'role-bulk-delete',
            'role-permanent-delete',
            'role-restore',
            'role-export',

            // User management
            'user-list',
            'user-create',
            'user-view',
            'user-edit',
            'user-delete',
            'user-bulk-delete',
            'user-restore',
            'user-export',
            'user-status',
            'user-direct-permission',

            // Permission management
            'permission-list',
            'permission-view',
            'permission-delete',
            'permission-bulk-delete',
            'permission-permanent-delete',
            'permission-restore',
            'permission-export',

            // Status management
            'status-list',
            'status-create',
            'status-view',
            'status-edit',
            'status-delete',
            'status-bulk-delete',
            'status-permanent-delete',
            'status-restore',
            'status-export',

            // Country management
            'country-list',
            'country-view',
            'country-delete',
            'country-permanent-delete',
            'country-restore',
            'country-export',

            // State management
            'state-list',
            'state-view',
            'state-delete',
            'state-permanent-delete',
            'state-restore',
            'state-export',

            // Lead management
            'lead-list',
            'lead-view',
            'lead-create',
            'lead-edit',
            'lead-delete',
            'lead-bulk-delete',
            'lead-permanent-delete',
            'lead-restore',
            'lead-export',
            'lead-status',
            'lead-assign-user',
            'lead-assigned-users-list',
            'lead-download',

            // Source management
            'source-list',
            'source-create',
            'source-view',
            'source-edit',
            'source-delete',
            'source-permanent-delete',
            'source-restore',
            'source-export',

            // Log Activity management
            'log-activity-list',
            'log-activity-create',
            'log-activity-view',
            'log-activity-edit',
            'log-activity-delete',
            'log-activity-bulk-delete',
            'log-activity-permanent-delete',
            'log-activity-restore',

            // Attachment management
            'attachment-list',
            'attachment-create',
            'attachment-view',
            'attachment-edit',
            'attachment-delete',
            'attachment-bulk-delete',
            'attachment-permanent-delete',
            'attachment-restore',
            'attachment-download',
        ];

        return $generalPermissions;
    }
}
