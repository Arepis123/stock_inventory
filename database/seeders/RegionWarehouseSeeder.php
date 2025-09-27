<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RegionWarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create regions
        $regions = [
            ['name' => 'East Coast', 'code' => 'east'],
            ['name' => 'West Coast', 'code' => 'west'],
            ['name' => 'North', 'code' => 'north'],
            ['name' => 'South', 'code' => 'south'],
        ];

        foreach ($regions as $regionData) {
            $region = \App\Models\Region::create($regionData);

            // Create warehouses for each region
            $warehouses = match($regionData['code']) {
                'east' => [
                    'Kuantan Warehouse',
                    'Kota Bharu Warehouse',
                    'Kuala Terengganu Warehouse',
                    'Mersing Warehouse'
                ],
                'west' => [
                    'Shah Alam Warehouse',
                    'Klang Warehouse',
                    'Port Dickson Warehouse',
                    'Melaka Warehouse'
                ],
                'north' => [
                    'Alor Setar Warehouse',
                    'George Town Warehouse',
                    'Ipoh Warehouse',
                    'Taiping Warehouse'
                ],
                'south' => [
                    'Johor Bahru Warehouse',
                    'Batu Pahat Warehouse',
                    'Muar Warehouse',
                    'Kluang Warehouse'
                ]
            };

            foreach ($warehouses as $warehouseName) {
                \App\Models\Warehouse::create([
                    'name' => $warehouseName,
                    'region_id' => $region->id,
                ]);
            }
        }
    }
}
