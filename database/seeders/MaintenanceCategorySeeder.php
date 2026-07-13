<?php

namespace Database\Seeders;

use App\Models\MaintenanceCategory;
use Illuminate\Database\Seeder;

class MaintenanceCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Plumbing', 'icon' => 'plumbing', 'description' => 'Leaking pipes, blocked drains, water heater issues'],
            ['name' => 'Electrical', 'icon' => 'electrical', 'description' => 'Power outages, faulty wiring, light fixtures'],
            ['name' => 'Cleaning', 'icon' => 'cleaning', 'description' => 'Common area cleaning, pest control, waste management'],
            ['name' => 'Security', 'icon' => 'security', 'description' => 'Gate issues, CCTV, access control, security lights'],
            ['name' => 'HVAC', 'icon' => 'hvac', 'description' => 'Air conditioning, heating, ventilation'],
            ['name' => 'Furniture', 'icon' => 'furniture', 'description' => 'Broken chairs, tables, cabinets, repairs'],
            ['name' => 'Pest Control', 'icon' => 'pest_control', 'description' => 'Rodents, insects, fumigation'],
            ['name' => 'Other', 'icon' => 'other', 'description' => 'Any other maintenance issue'],
        ];

        foreach ($categories as $category) {
            MaintenanceCategory::create([
                'facility_id' => 1, // Default facility
                'name' => $category['name'],
                'icon' => $category['icon'],
                'description' => $category['description'],
                'is_active' => true,
            ]);
        }
    }
}