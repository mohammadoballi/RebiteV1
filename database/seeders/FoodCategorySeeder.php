<?php

namespace Database\Seeders;

use App\Models\FoodCategory;
use Illuminate\Database\Seeder;

class FoodCategorySeeder extends Seeder
{
    /**
     * Hierarchical food taxonomy for donor selection and charity filters.
     */
    public function run(): void
    {
        $tree = [
            'Arabian' => ['Jordanian Food', 'Lebanese Food', 'Syrian Food', 'Mixed Arabic Meals'],
            'Asian' => ['Chinese Dishes', 'Japanese Dishes', 'Korean Dishes', 'Mixed Asian Meals'],
            'European' => ['Italian', 'French', 'British', 'Other'],
            'American' => ['Fast Food', 'BBQ & Grilled Food', 'Other'],
            'Fresh Food' => ['Salads', 'Fresh Juices', 'Fruits & Vegetables', 'Other'],
        ];

        $order = 0;
        foreach ($tree as $parentName => $children) {
            $parent = FoodCategory::create([
                'parent_id' => null,
                'name' => $parentName,
                'sort_order' => $order++,
            ]);
            $c = 0;
            foreach ($children as $childName) {
                FoodCategory::create([
                    'parent_id' => $parent->id,
                    'name' => $childName,
                    'sort_order' => $c++,
                ]);
            }
        }
    }
}
