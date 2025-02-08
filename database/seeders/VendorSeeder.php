<?php

namespace Database\Seeders;

use App\Models\Vendor;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = [
            ['label' => 'Shippo'],
            ['label' => 'EasyShip'],
            ['label' => 'USPS (No Logo)'],
            ['label' => 'Rollo'],
            ['label' => 'Shoppify'],
            ['label' => 'EVS'],
            ['label' => 'ATFM'],
            ['label' => 'Easypost'],
            ['label' => 'Pitney Bowes'],
            ['label' => 'Shippo (New)'],
        ];

        foreach ($vendors as $vendor) {
            Vendor::create([
                'name' => $vendor['label'],
                'slug' => Str::slug($vendor['label'])
            ]);
        }
    }
}
