<?php

namespace Database\Seeders;

use App\Models\Industry;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class IndustrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $industries = [
            'Technology',
            'Healthcare',
            'Finance',
            'Education',
            'Retail',
            'Manufacturing',
            'Construction',
            'Agriculture',
            'Transportation',
            'Real Estate',
            'Energy',
            'Telecommunications',
            'Entertainment',
            'Food & Beverage',
            'Consulting',
            'Marketing',
            'Legal Services',
            'Tourism & Hospitality',
            'Automotive',
            'Pharmaceuticals',
        ];

        foreach ($industries as $industry) {
            Industry::create([
                'name' => $industry,
                'slug' => Str::slug($industry),
            ]);
        }
    }
}
