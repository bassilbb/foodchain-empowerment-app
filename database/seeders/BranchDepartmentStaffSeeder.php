<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Staff;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BranchDepartmentStaffSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = AdminUser::first();
        if (! $adminUser) {
            $adminUser = AdminUser::create([
                'name' => 'Admin User',
                'email' => 'admin@foodchain.com',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
            ]);
        }

        $branches = [
            [
                'name' => 'Lagos Mainland Branch',
                'code' => 'LAG-ML',
                'address' => '15 Admiralty Way, Lekki Phase 1',
                'city' => 'Lagos',
                'state' => 'Lagos',
                'phone' => '+234 801 234 5678',
                'email' => 'lagos@foodchain.com',
            ],
            [
                'name' => 'Abuja Central Branch',
                'code' => 'ABJ-CT',
                'address' => 'Plot 256, Ademola Adetokunbo Crescent, Wuse 2',
                'city' => 'Abuja',
                'state' => 'FCT',
                'phone' => '+234 802 345 6789',
                'email' => 'abuja@foodchain.com',
            ],
            [
                'name' => 'Port Harcourt Branch',
                'code' => 'PH-GR',
                'address' => '26 Ordinance Road, GRA Phase 2',
                'city' => 'Port Harcourt',
                'state' => 'Rivers',
                'phone' => '+234 803 456 7890',
                'email' => 'portharcourt@foodchain.com',
            ],
        ];

        foreach ($branches as $branchData) {
            $branch = Branch::updateOrCreate(
                ['code' => $branchData['code']],
                $branchData
            );

            $departments = [
                [
                    'name' => 'Operations',
                    'code' => $branch->code.'-OPS',
                    'description' => 'Handles day-to-day operations and logistics',
                ],
                [
                    'name' => 'Customer Service',
                    'code' => $branch->code.'-CS',
                    'description' => 'Handles customer inquiries and complaints',
                ],
                [
                    'name' => 'Technical Support',
                    'code' => $branch->code.'-TECH',
                    'description' => 'Handles technical issues and maintenance',
                ],
                [
                    'name' => 'Safety & Compliance',
                    'code' => $branch->code.'-SAFE',
                    'description' => 'Handles safety protocols and compliance',
                ],
            ];

            foreach ($departments as $deptData) {
                $department = Department::updateOrCreate(
                    ['code' => $deptData['code']],
                    [
                        'branch_id' => $branch->id,
                        'name' => $deptData['name'],
                        'description' => $deptData['description'],
                    ]
                );

                $positions = ['Manager', 'Supervisor', 'Officer', 'Assistant'];
                $staffCount = Department::where('branch_id', $branch->id)->count() * 4;

                foreach ($positions as $position) {
                    $staffCount++;
                    $staffNumber = 'STF-'.strtoupper(substr($branch->code, 0, 3)).'-'.$staffCount;

                    Staff::updateOrCreate(
                        ['staff_number' => $staffNumber],
                        [
                            'department_id' => $department->id,
                            'admin_user_id' => $adminUser->id,
                            'position' => $position,
                            'phone' => '+234 '.rand(800, 999).' '.rand(100, 999).' '.rand(1000, 9999),
                        ]
                    );
                }
            }
        }
    }
}
