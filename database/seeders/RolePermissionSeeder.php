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
                [ 'name' => 'Admin', 'guard_name' => 'user'],
                [ 'name' => 'Lead', 'guard_name' => 'user'],
                [ 'name' => 'Agent', 'guard_name' => 'user'],
            ];
            
            collect($roles)->each(function ($role) {
                Role::firstOrCreate($role);
            });

            $adminRole = Role::where('name','Admin')->first();

            // Define permissions by module
            $permissions = $this->getPermissions('user');

            // Create permissions if they don't exist
            foreach ($permissions as $permission) {
                $underscoreSeparated = explode('-', $permission);
                $label = str_replace('_', ' ', $underscoreSeparated[0]);
                $exists = DB::table('permissions')
                    ->where('label', $label)
                    ->where('name', $permission)
                    ->exists();

                if ($exists) {
                    continue;
                }
                Permission::create([
                    'label' => $label,
                    'name' => $permission,
                    'guard_name' => 'user',
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
            'role-restore',

            // User management
            'user-list',
            'user-create',
            'user-view',
            'user-edit',
            'user-delete',
            'user-restore',
            'user-status',
            'user-direct_permission',
            'user-impersonate',

            // Permission management
            'permission-list',
            'permission-view',
            'permission-delete',
            'permission-restore',

            // Status management
            'status-list',
            'status-create',
            'status-view',
            'status-edit',
            'status-delete',
            'status-restore',

            // Country management
            'country-list',
            'country-view',
            'country-delete',
            'country-restore',

            // State management
            'state-list',
            'state-view',
            'state-delete',
            'state-restore',

            // Lead management
            'lead-list',
            'lead-view',
            'lead-create',
            'lead-edit',
            'lead-delete',
            'lead-restore',
            'lead-status',
            'lead-assign',
            'lead-assignees',

            // Source management
            'source-list',
            'source-create',
            'source-view',
            'source-edit',
            'source-delete',
            'source-restore',

            // Log Activity management
            'log_activity-list',
            'log_activity-view',
            'log_activity-delete',
            'log_activity-restore',

            // Attachment management
            'attachment-list',
            'attachment-create',
            'attachment-view',
            'attachment-edit',
            'attachment-delete',
            'attachment-restore',
            'attachment-download',

            //Campaign management
            'campaign-list',
            'campaign-create',
            'campaign-view',
            'campaign-edit',
            'campaign-delete',
            'campaign-restore',
            'campaign-status',

            //Lead Capture management
            'lead_capture-list',
            'lead_capture-create',
            'lead_capture-view',
            'lead_capture-edit',
            'lead_capture-delete',
            'lead_capture-restore',
            'lead_capture-status',

            //Faq management
            'faq-list',
            'faq-create',
            'faq-view',
            'faq-edit',
            'faq-delete',
            'faq-restore',
            'faq-status',

            //Notification management
            'notification-list',
            'notification-view',
            'notification-delete',

            //activity log management
            'activity_log-list',
            'activity_log-view',
            'activity_log-delete',

            //Meeting management
            'meeting-list',
            'meeting-create',
            'meeting-view',
            'meeting-edit',
            'meeting-delete',
            'meeting-restore',
            'meeting-status',
        ];

        return $generalPermissions;
    }
}
