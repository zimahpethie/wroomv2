<?php

use App\Models\Department;
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
        Department::insert([
            [
                'name' => 'PEJABAT REKTOR',
                'publish_status' => true
            ],
            [
                'name' => 'BAHAGIAN HAL EHWAL AKADEMIK & ANTARABANGSA',
                'publish_status' => true
            ],
            [
                'name' => 'BAHAGIAN HAL EHWAL PELAJAR',
                'publish_status' => true
            ],
            [
                'name' => 'BAHAGIAN PENYELIDIKAN DAN JARINGAN INDUSTRI',
                'publish_status' => true
            ],
            [
                'name' => 'BAHAGIAN PENTADBIRAN',
                'publish_status' => true
            ],
            [
                'name' => 'BAHAGIAN PENTADBIRAN (KAMPUS MUKAH)',
                'publish_status' => true
            ],
            [
                'name' => 'PEJABAT BENDAHARI',
                'publish_status' => true
            ],
            [
                'name' => 'BAHAGIAN PERPUSTAKAAN',
                'publish_status' => true
            ],
            [
                'name' => 'BAHAGIAN PENGURUSAN FASILITI',
                'publish_status' => true
            ],
            [
                'name' => 'UNIT KUALITI',
                'publish_status' => true
            ],
            [
                'name' => 'UNIT PERANCANGAN STRATEGIK',
                'publish_status' => true
            ],
            [
                'name' => 'BAHAGIAN INFOSTRUKTUR',
                'publish_status' => true
            ],
            [
                'name' => 'PEJABAT POLIS BANTUAN',
                'publish_status' => true
            ],
            [
                'name' => 'UNIT KOMUNIKASI KORPORAT',
                'publish_status' => true
            ],
        ]);
    }
}
