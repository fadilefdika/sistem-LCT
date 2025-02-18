<?php

namespace Database\Seeders;

use App\Models\DataFeed;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DashboardTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('datafeeds')->insert([
            'data_type' => 1,
            'label' => '12-01-2020',  // Memberikan nilai pada kolom label
            'data' => 532,
            'dataset_name' => 2,
            'updated_at' => now(),
            'created_at' => now(),
        ]);
    }
}
