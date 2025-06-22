<?php

use App\Models\Tahun;
use Illuminate\Database\Seeder;

class TahunSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Tahun::insert([
            [
                'tahun' => 2019,
                'publish_status' => true
            ],
            [
                'tahun' => 2020,
                'publish_status' => true
            ],
            [
                'tahun' => 2021,
                'publish_status' => true
            ],
            [
                'tahun' => 2022,
                'publish_status' => true
            ],
            [
                'tahun' => 2023,
                'publish_status' => true
            ],
            [
                'tahun' => 2024,
                'publish_status' => true
            ],
        ]);
    }
}
