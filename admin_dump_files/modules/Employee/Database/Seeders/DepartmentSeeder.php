<?php

namespace Modules\Employee\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $data = [
            'IT', 'HR', 'Finance', 'Marketing', 'Sales', 'Production', 'Quality Control', 'Research and Development', 'Customer Service', 'Logistics', 'Warehouse', 'Maintenance', 'Security', 'Administration', 'Legal', 'Purchasing', 'Accounting', 'Engineering', 'Management', 'Others',
        ];
        foreach ($data as $value) {
            \Modules\Employee\Entities\Department::create([
                'name' => $value,
            ]);
        }
    }
}
