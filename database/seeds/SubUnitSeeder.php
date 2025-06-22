<?php

use App\Models\SubUnit;
use Illuminate\Database\Seeder;

class SubUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SubUnit::insert([
            [
                'department_id' => 2,
                'name' => 'Unit Penilaian Akademik',
                'publish_status' => true
            ],
            [
                'department_id' => 2,
                'name' => 'Unit Konvokesyen',
                'publish_status' => true
            ],
            [
                'department_id' => 2,
                'name' => 'Unit Pengambilan',
                'publish_status' => true
            ],
            [
                'department_id' => 2,
                'name' => 'Unit Pengajaran dan Pembelajaran',
                'publish_status' => true
            ],
            [
                'department_id' => 2,
                'name' => 'Unit Am dan Rekod Pelajar',
                'publish_status' => true
            ],
            [
                'department_id' => 2,
                'name' => 'Unit UiTM Global (Cawangan Sarawak)',
                'publish_status' => true
            ],
            [
                'department_id' => 2,
                'name' => 'Unit Pascasiswazah/CGS',
                'publish_status' => true
            ],
            [
                'department_id' => 2,
                'name' => 'Unit Perancangan Strategik (BHEA)',
                'publish_status' => true
            ],
            [
                'department_id' => 3,
                'name' => 'Unit AM',
                'publish_status' => true
            ],
            [
                'department_id' => 3,
                'name' => 'Unit Kaunseling',
                'publish_status' => true
            ],
            [
                'department_id' => 3,
                'name' => 'Unit Kokurikulum',
                'publish_status' => true
            ],
            [
                'department_id' => 3,
                'name' => 'Unit Kebudayaan',
                'publish_status' => true
            ],
            [
                'department_id' => 3,
                'name' => 'Unit Residensi dan Hospitaliti Pelajar',
                'publish_status' => true
            ],
            [
                'department_id' => 3,
                'name' => 'Unit Kesihatan',
                'publish_status' => true
            ],
            [
                'department_id' => 3,
                'name' => 'Unit Sukan',
                'publish_status' => true
            ],
            [
                'department_id' => 3,
                'name' => 'Unit Kepimpinan Pelajar',
                'publish_status' => true
            ],
        ]);
    }
}
