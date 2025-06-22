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
                'name' => 'Pejabat Rektor',
                'publish_status' => true
            ],
            [
                'name' => 'Bahagian Hal Ehwal Akademik dan Antarabangsa',
                'publish_status' => true
            ],
            [
                'name' => 'Bahagian Hal Ehwal Pelajar',
                'publish_status' => true
            ],
            [
                'name' => 'Bahagian Penyelidikan dan Jaringan Industri',
                'publish_status' => true
            ],
            [
                'name' => 'Bahagian Pentadbiran',
                'publish_status' => true
            ],
            [
                'name' => 'Bahagian Pentadbiran (Kampus Mukah)',
                'publish_status' => true
            ],
            [
                'name' => 'Pejabat Bendahari',
                'publish_status' => true
            ],
            [
                'name' => 'Bahagian Perpustakaan',
                'publish_status' => true
            ],
            [
                'name' => 'Bahagian Pengurusan Fasiliti',
                'publish_status' => true
            ],
            [
                'name' => 'Unit Kualiti',
                'publish_status' => true
            ],
            [
                'name' => 'Unit Perancangan Strategik',
                'publish_status' => true
            ],
            [
                'name' => 'Bahagian Infostruktur',
                'publish_status' => true
            ],
            [
                'name' => 'Pejabat Polis Bantuan',
                'publish_status' => true
            ],
            [
                'name' => 'Unit Komunikasi Korporat',
                'publish_status' => true
            ],
        ]);
    }
}
