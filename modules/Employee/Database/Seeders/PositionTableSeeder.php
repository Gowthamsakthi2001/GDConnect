<?php

namespace Modules\Employee\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class PositionTableSeeder extends Seeder
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
            'Developer',
            'Software Engineer',
            'Project Manager',

        ];

        foreach ($data as $key => $value) {
            \Modules\Employee\Entities\Position::create([
                'name' => $value,
                'is_active' => true,
            ]);
        }
    }
}
