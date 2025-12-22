<?php

namespace Database\Seeders;

use App\Models\BusinessCategory;
use Illuminate\Database\Seeder;

class BusinessCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Agriculture',
            'Retail',
            'Services',
            'Manufacturing',
            'Trading',
            'Fisheries',
            'Animal Husbandry',
            'Construction',
            'Transport',
            'Other'
        ];

        foreach ($categories as $category) {
            BusinessCategory::firstOrCreate(
                ['name' => $category],
                [
                    'created_by' => 1, // Assuming admin user ID is 1
                    'updated_by' => 1,
                ]
            );
        }
    }
}
