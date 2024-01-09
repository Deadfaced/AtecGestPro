<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaterialClothingDeliverySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Seed 1
        DB::table('material__clothing__deliveries')->insert([
            'clothing_delivery_id' => 1,
            'material_id' => 6,
            'quantity' => 2,
        ]);
        // Seed 2
        DB::table('material__clothing__deliveries')->insert([
            'clothing_delivery_id' => 2,
            'material_id' => 2,
            'quantity' => 1,
        ]);

    }
}
