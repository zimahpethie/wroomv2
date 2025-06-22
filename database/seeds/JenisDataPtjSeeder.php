<?php

use App\Models\JenisDataPtj;
use Illuminate\Database\Seeder;

class JenisDataPtjSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        JenisDataPtj::insert([
            [
                'name' => 'Data 6 Star Rating Final Exam',
                'department_id' => 2,
                'subunit_id' => 1,
                'publish_status' => true
            ],
            [
                'name' => 'Data GOT',
                'department_id' => 2,
                'subunit_id' => 1,
                'publish_status' => true
            ]
        ]);
    }
}
