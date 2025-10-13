<?php

namespace Modules\Employee\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
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
            'CEO', 'CFO', 'COO', 'CTO', 'CMO', 'CIO', 'CISO', 'CRO', 'CDO', 'CLO', 'CHRO', 'CSO', 'CPO', 'CQO',  'CVO', 'CBO', 'CNO', 'CWO',
        ];
        foreach ($data as $value) {
            \Modules\Employee\Entities\Position::create([
                'name' => $value,
            ]);
        }
    }
}
